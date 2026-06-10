<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice #{{ $pesanan->idpesanan }} - Kantin Online</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .invoice-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            max-width: 480px;
            width: 100%;
            overflow: hidden;
        }

        /* Header */
        .invoice-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 28px 24px;
            text-align: center;
        }

        .invoice-header h1 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .invoice-header p {
            font-size: 13px;
            opacity: 0.85;
        }

        /* Status badge */
        .status-badge {
            display: inline-block;
            margin-top: 12px;
            padding: 6px 18px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .status-lunas {
            background: rgba(255,255,255,0.25);
            color: #fff;
            border: 1.5px solid rgba(255,255,255,0.5);
        }

        .status-pending {
            background: #ffc107;
            color: #333;
        }

        /* Body */
        .invoice-body {
            padding: 24px;
        }

        /* Info rows */
        .info-section {
            margin-bottom: 20px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #888;
            font-weight: 500;
        }

        .info-value {
            color: #333;
            font-weight: 600;
            text-align: right;
            max-width: 60%;
            word-break: break-all;
        }

        /* Divider */
        .divider {
            height: 1px;
            background: #e8e8e8;
            margin: 16px 0;
        }

        /* Section title */
        .section-title {
            font-size: 13px;
            font-weight: 600;
            color: #667eea;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 12px;
        }

        /* Item list */
        .item-list {
            list-style: none;
        }

        .item-list li {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 10px 0;
            border-bottom: 1px dashed #eee;
            font-size: 14px;
        }

        .item-list li:last-child {
            border-bottom: none;
        }

        .item-name {
            color: #333;
            font-weight: 500;
            flex: 1;
        }

        .item-vendor {
            font-size: 11px;
            color: #667eea;
            font-weight: 600;
        }

        .item-qty {
            color: #888;
            font-size: 13px;
            min-width: 40px;
            text-align: center;
        }

        .item-subtotal {
            color: #333;
            font-weight: 600;
            min-width: 90px;
            text-align: right;
        }

        .item-note {
            font-size: 11px;
            color: #e74c3c;
            margin-top: 2px;
        }

        /* Total */
        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 0 0;
        }

        .total-label {
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }

        .total-value {
            font-size: 22px;
            font-weight: 700;
            color: #667eea;
        }

        /* QR Code */
        .qr-section {
            text-align: center;
            padding: 20px 24px 24px;
            background: #fafafa;
            border-top: 1px solid #f0f0f0;
        }

        .qr-section img {
            width: 150px;
            height: 150px;
        }

        .qr-section p {
            font-size: 11px;
            color: #aaa;
            margin-top: 8px;
        }

        /* Footer */
        .invoice-footer {
            text-align: center;
            padding: 16px 24px;
            font-size: 12px;
            color: #bbb;
            background: #fafafa;
        }
    </style>
</head>
<body>

<div class="invoice-card">
    {{-- Header --}}
    <div class="invoice-header">
        <h1>Invoice Pesanan</h1>
        <p>Kantin Online - #{{ str_pad($pesanan->idpesanan, 6, '0', STR_PAD_LEFT) }}</p>

        @if($pesanan->status_bayar == 1)
            <span class="status-badge status-lunas">✓ LUNAS</span>
        @else
            <span class="status-badge status-pending">⏳ MENUNGGU PEMBAYARAN</span>
        @endif
    </div>

    {{-- Body --}}
    <div class="invoice-body">
        {{-- Info --}}
        <div class="info-section">
            <div class="section-title">Informasi Pesanan</div>
            <div class="info-row">
                <span class="info-label">ID Pesanan</span>
                <span class="info-value">{{ $pesanan->idpesanan }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Order ID</span>
                <span class="info-value">{{ $pesanan->midtrans_order_id }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Pemesan</span>
                <span class="info-value">{{ $pesanan->nama }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Waktu</span>
                <span class="info-value">{{ $pesanan->timestamp }}</span>
            </div>
        </div>

        <div class="divider"></div>

        {{-- Detail Items --}}
        <div class="section-title">Detail Pesanan</div>
        <ul class="item-list">
            @foreach($pesanan->details as $d)
            <li>
                <div class="item-name">
                    @if($d->menu && $d->menu->vendor)
                        <span class="item-vendor">[{{ $d->menu->vendor->nama_vendor }}]</span><br>
                    @endif
                    {{ $d->menu->nama_menu ?? 'Menu Terhapus' }}
                    @if($d->catatan)
                        <div class="item-note">📝 {{ $d->catatan }}</div>
                    @endif
                </div>
                <span class="item-qty">x{{ $d->jumlah }}</span>
                <span class="item-subtotal">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</span>
            </li>
            @endforeach
        </ul>

        <div class="divider"></div>

        {{-- Total --}}
        <div class="total-row">
            <span class="total-label">Total</span>
            <span class="total-value">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- QR Code --}}
    <div class="qr-section">
        <img src="{{ $qrCodeDataUri }}" alt="QR Code Invoice">
        <p>Scan QR untuk verifikasi pesanan</p>
    </div>

    {{-- Footer --}}
    <div class="invoice-footer">
        Terima kasih atas pesanan Anda 🙏
    </div>
</div>

</body>
</html>
