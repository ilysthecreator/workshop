<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ============================================
    // VENDOR CRUD
    // ============================================
    public function index()
    {
        $vendors = Vendor::all();
        return view('vendor.index', compact('vendors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_vendor' => 'required|string|max:255',
        ]);

        Vendor::create([
            'nama_vendor' => $request->nama_vendor,
            'user_id' => null, // we don't bind to user anymore
        ]);

        return redirect()->back()->with('success', 'Vendor berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->delete();
        return redirect()->back()->with('success', 'Vendor berhasil dihapus.');
    }

    // ============================================
    // MENU CRUD
    // ============================================
    public function menu()
    {
        $menus = Menu::with('vendor')->get();
        $vendors = Vendor::all();
        return view('vendor.menu.index', compact('menus', 'vendors'));
    }

    public function storeMenu(Request $request)
    {
        $request->validate([
            'idvendor' => 'required|exists:vendor,idvendor',
            'nama_menu' => 'required',
            'harga' => 'required|integer',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $path = null;
        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('menus', 'public');
        }

        Menu::create([
            'idvendor' => $request->idvendor,
            'nama_menu' => $request->nama_menu,
            'harga' => $request->harga,
            'path_gambar' => $path
        ]);

        return redirect()->back()->with('success', 'Menu berhasil ditambahkan.');
    }

    public function destroyMenu($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->delete();
        return redirect()->back()->with('success', 'Menu berhasil dihapus.');
    }

    public function pesanan()
    {
        $pesanan = Pesanan::with(['details.menu.vendor'])
            ->orderBy('timestamp', 'DESC')
            ->get();

        return view('vendor.pesanan.index', compact('pesanan'));
    }

    public function scanQR()
    {
        return view('vendor.scan');
    }

    public function getPesananByQR($idpesanan)
    {
        $pesanan = Pesanan::with(['details.menu.vendor'])->find($idpesanan);

        if (!$pesanan) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan dengan ID ' . $idpesanan . ' tidak ditemukan.',
            ], 404);
        }

        $items = [];
        foreach ($pesanan->details as $d) {
            $items[] = [
                'nama_menu' => $d->menu->nama_menu ?? 'Menu Terhapus',
                'vendor' => $d->menu->vendor->nama_vendor ?? '-',
                'jumlah' => $d->jumlah,
                'harga' => $d->harga,
                'harga_format' => 'Rp ' . number_format($d->harga, 0, ',', '.'),
                'subtotal' => $d->subtotal,
                'subtotal_format' => 'Rp ' . number_format($d->subtotal, 0, ',', '.'),
                'catatan' => $d->catatan,
            ];
        }

        return response()->json([
            'success' => true,
            'pesanan' => [
                'idpesanan' => $pesanan->idpesanan,
                'nama' => $pesanan->nama,
                'total' => $pesanan->total,
                'total_format' => 'Rp ' . number_format($pesanan->total, 0, ',', '.'),
                'status_bayar' => $pesanan->status_bayar,
                'status_label' => $pesanan->status_bayar == 1 ? 'LUNAS' : 'BELUM LUNAS',
                'midtrans_order_id' => $pesanan->midtrans_order_id,
                'timestamp' => $pesanan->timestamp,
            ],
            'items' => $items,
        ]);
    }
}
