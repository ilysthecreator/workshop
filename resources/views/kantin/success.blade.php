@extends('layouts.master')

@section('content')
<div class="row justify-content-center mt-5">
  <div class="col-md-6 text-center">
    <div class="card shadow">
      <div class="card-body">
        
        @if($pesanan->status_bayar == 1)
            <i class="mdi mdi-check-circle text-success" style="font-size: 80px;"></i>
            <h2 class="text-success font-weight-bold mt-3">Pembayaran Berhasil!</h2>
            <p class="text-muted">Terima kasih, pesanan Anda (LUNAS) sedang diproses oleh kantin.</p>
        @else
            <i class="mdi mdi-clock-time-three-outline text-warning" style="font-size: 80px;"></i>
            <h2 class="text-warning font-weight-bold mt-3">Menunggu Pembayaran</h2>
            <p class="text-muted">Silahkan selesaikan instruksi pembayaran Anda pada menu Midtrans.</p>
        @endif

        <div class="mt-4 p-4 border rounded text-left bg-light">
          <p class="mb-2"><strong>ID Order &nbsp;:</strong> {{ $pesanan->midtrans_order_id }}</p>
          <p class="mb-2"><strong>Pemesan :</strong> {{ $pesanan->nama }}</p>
          <p class="mb-2"><strong>Total &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</strong> Rp {{ number_format($pesanan->total, 0, ',', '.') }}</p>
          <p class="mb-0"><strong>Status &nbsp;&nbsp;&nbsp;&nbsp;:</strong> 
            @if($pesanan->status_bayar == 1)
                <span class="badge badge-success">Selesai / Lunas</span>
            @else
                <span class="badge badge-warning text-dark">Belum Lunas / Pending</span>
            @endif
          </p>
        </div>

        {{-- QR Code containing idpesanan --}}
        @if(isset($qrCodeDataUri))
        <div class="mt-4">
          <h5 class="font-weight-bold">QR Code Pesanan</h5>
          <img src="{{ $qrCodeDataUri }}" alt="QR Code ID Pesanan {{ $pesanan->idpesanan }}" class="img-fluid" style="max-width: 200px;">
          <p class="text-muted mt-2 mb-0"><small>ID Pesanan: {{ $pesanan->idpesanan }}</small></p>

          <div class="alert alert-info mt-3 text-left shadow-sm" role="alert" style="border-left: 5px solid #17a2b8;">
              <h5 class="alert-heading font-weight-bold"><i class="mdi mdi-information"></i> PETUNJUK</h5>
              <p class="mb-0 text-dark">
                  Tunjukkan QR Code ini kepada <strong>Kasir / Vendor</strong> untuk mengambil pesanan Anda.
              </p>
              <hr>
              <p class="mb-0 text-muted" style="font-size: 13px;">
                  <i class="mdi mdi-bookmark"></i> Anda bisa mengakses halaman ini kembali melalui link berikut:<br>
                  <a href="{{ route('kantin.success', ['idpesanan' => $pesanan->midtrans_order_id]) }}" class="font-weight-bold">
                    {{ route('kantin.success', ['idpesanan' => $pesanan->midtrans_order_id]) }}
                  </a>
              </p>
          </div>
        </div>
        @endif

        <a href="{{ url('kantin') }}" class="btn btn-gradient-primary mt-4 btn-lg">Pesan Makanan Lain</a>
      </div>
    </div>
  </div>
</div>
@endsection

