<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Vendor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class KantinController extends Controller
{
    public function __construct()
    {
        // Set konfigurasi midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function index()
    {
        $vendors = Vendor::all();
        return view('kantin.index', compact('vendors'));
    }

    public function getMenuByVendor($idvendor)
    {
        $menus = Menu::where('idvendor', $idvendor)->get();
        return response()->json([
            'success' => true,
            'data' => $menus
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.idmenu' => 'required|integer',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga' => 'required|integer|min:0',
            'items.*.subtotal' => 'required|integer|min:0',
            'items.*.catatan' => 'nullable|string|max:255',
            'total' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // 1. Buat Guest User otomatis (Optional/Dummy Name)
            $lastPesanan = Pesanan::orderBy('idpesanan', 'desc')->first();
            $nextId = $lastPesanan ? $lastPesanan->idpesanan + 1 : 1;
            $namaGuest = 'Guest_' . str_pad($nextId, 7, '0', STR_PAD_LEFT);

            // 2. Simpan Pesanan
            $orderId = 'TRX-' . time() . '-' . uniqid(); // Unique Midtrans Order ID
            $pesanan = Pesanan::create([
                'nama' => $namaGuest,
                'total' => $request->total,
                'metode_bayar' => 0, // Akan diset oleh midtrans pada callback jika dibutuhkan
                'status_bayar' => 0, // Belum lunas
                'midtrans_order_id' => $orderId,
            ]);

            // 3. Simpan Detail Pesanan
            $itemDetails = [];
            foreach ($request->items as $item) {
                // Get menu to ensure correctness
                $menu = Menu::find($item['idmenu']);
                if ($menu) {
                    DetailPesanan::create([
                        'idpesanan' => $pesanan->idpesanan,
                        'idmenu' => $item['idmenu'],
                        'jumlah' => $item['jumlah'],
                        'harga' => $item['harga'],
                        'subtotal' => $item['subtotal'],
                        'catatan' => $item['catatan'],
                    ]);

                    $itemDetails[] = [
                        'id' => $menu->idmenu,
                        'price' => $menu->harga,
                        'quantity' => $item['jumlah'],
                        'name' => Str::limit($menu->nama_menu, 50),
                    ];
                }
            }

            // 4. Proses Request Token Snap Midtrans
            $transaction = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $request->total,
                ],
                'customer_details' => [
                    'first_name' => $namaGuest,
                ],
                'item_details' => $itemDetails,
            ];

            $snapToken = Snap::getSnapToken($transaction);
            
            DB::commit();

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'order_id' => $orderId,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal meproses pesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function success($order_id)
    {
        $pesanan = Pesanan::where('midtrans_order_id', $order_id)->firstOrFail();

        // [PENTING UNTUK LOCALHOST] Cek dan sinkronisasi status ke Midtrans secara real-time
        // Karena webhook sering tidak jalan di localhost, kita cek manual ke Midtrans saat redirect
        try {
            $status_response = \Midtrans\Transaction::status($pesanan->midtrans_order_id);
            $status_obj = is_array($status_response) ? (object) $status_response : $status_response;
            $transaction_status = $status_obj->transaction_status ?? '';
            $type = $status_obj->payment_type ?? '';
            $fraud = $status_obj->fraud_status ?? '';

            if ($transaction_status == 'capture') {
                $pesanan->update(['status_bayar' => ($type == 'credit_card' && $fraud == 'challenge') ? 0 : 1]);
            } else if ($transaction_status == 'settlement') {
                $pesanan->update(['status_bayar' => 1]);
            } else if (in_array($transaction_status, ['deny', 'expire', 'cancel'])) {
                $pesanan->update(['status_bayar' => 0]);
            }
        } catch (\Exception $e) {
            // Abaikan error jaringan agar halaman tetap bisa terbuka
        }

        // Generate QR Code containing idpesanan (untuk di-scan oleh Vendor)
        $qrCodeDataUri = $this->generateQrCodeDataUri((string) $pesanan->idpesanan);

        return view('kantin.success', compact('pesanan', 'qrCodeDataUri'));
    }

    public function invoice($idpesanan)
    {
        $pesanan = Pesanan::with('details.menu.vendor')->findOrFail($idpesanan);

        // Generate QR Code containing idpesanan
        $qrCodeDataUri = $this->generateQrCodeDataUri((string) $pesanan->idpesanan);

        return view('kantin.invoice', compact('pesanan', 'qrCodeDataUri'));
    }

    public function callback(Request $request)
    {
        try {
            $notification = new Notification();
            
            $transaction_status = $notification->transaction_status;
            $type = $notification->payment_type;
            $order_id = $notification->order_id;
            $fraud = $notification->fraud_status;

            $pesanan = Pesanan::where('midtrans_order_id', $order_id)->first();

            if (!$pesanan) {
                return response()->json(['message' => 'Pesanan tidak ditemukan'], 404);
            }

            if ($transaction_status == 'capture') {
                if ($type == 'credit_card') {
                    if ($fraud == 'challenge') {
                        $pesanan->update(['status_bayar' => 0]); 
                    } else {
                        $pesanan->update(['status_bayar' => 1]); 
                    }
                }
            } else if ($transaction_status == 'settlement') {
                $pesanan->update(['status_bayar' => 1]); 
            } else if ($transaction_status == 'pending') {
                $pesanan->update(['status_bayar' => 0]); 
            } else if ($transaction_status == 'deny' || $transaction_status == 'expire' || $transaction_status == 'cancel') {
                $pesanan->update(['status_bayar' => 0]); 
            }

            return response()->json(['success' => true, 'message' => 'Notifikasi diproses']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Sinkronisasi Manual Status Midtrans dari Panel Admin (Penting jika Webhook tidak jalan di Localhost)
     */
    public function syncStatus($idpesanan)
    {
        try {
            $pesanan = Pesanan::findOrFail($idpesanan);

            if (!$pesanan->midtrans_order_id) {
                return redirect()->back()->with('error', 'Pesanan tidak memiliki ID Transaksi Midtrans.');
            }

            $status_response = \Midtrans\Transaction::status($pesanan->midtrans_order_id);
            // Paksa menjadi object agar aman saat dipanggil
            $status_obj = is_array($status_response) ? (object) $status_response : $status_response;
            
            $transaction_status = $status_obj->transaction_status ?? '';
            $type = $status_obj->payment_type ?? '';
            $fraud = $status_obj->fraud_status ?? '';

            if ($transaction_status == 'capture') {
                if ($type == 'credit_card' && $fraud == 'challenge') {
                    $pesanan->update(['status_bayar' => 0]);
                } else {
                    $pesanan->update(['status_bayar' => 1]);
                }
            } else if ($transaction_status == 'settlement') {
                $pesanan->update(['status_bayar' => 1]);
            } else if ($transaction_status == 'pending') {
                $pesanan->update(['status_bayar' => 0]);
            } else if ($transaction_status == 'deny' || $transaction_status == 'expire' || $transaction_status == 'cancel') {
                $pesanan->update(['status_bayar' => 0]);
            }

            return redirect()->back()->with('success', 'Status pesanan berhasil disinkronisasi dari Midtrans.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memanggil API Midtrans: ' . $e->getMessage());
        }
    }

    /**
     * AJAX: Return struk data + QR Code for inline modal
     */
    public function getStrukData($idpesanan)
    {
        $pesanan = Pesanan::with('details.menu')->findOrFail($idpesanan);

        // Generate QR Code containing idpesanan
        $qrCodeDataUri = $this->generateQrCodeDataUri((string) $pesanan->idpesanan);

        return response()->json([
            'success' => true,
            'pesanan' => [
                'idpesanan' => $pesanan->idpesanan,
                'nama' => $pesanan->nama,
                'total' => number_format($pesanan->total, 0, ',', '.'),
                'status_bayar' => $pesanan->status_bayar,
                'midtrans_order_id' => $pesanan->midtrans_order_id,
                'timestamp' => $pesanan->timestamp,
            ],
            'qr_code' => $qrCodeDataUri,
        ]);
    }

    /**
     * Generate base64 QR Code string from URL
     */
    private function generateQrCodeDataUri($content)
    {
        $qrCode = QrCode::create($content)
            ->setSize(200)
            ->setMargin(10);

        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        return 'data:image/png;base64,' . base64_encode($result->getString());
    }
}
