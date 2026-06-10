@extends('layouts.master')

@section('content')
<div class="page-header">
  <h3 class="page-title">
    <span class="page-title-icon bg-gradient-success text-white me-2">
      <i class="mdi mdi-playlist-check"></i>
    </span> Semua Pesanan Kantin
  </h3>
</div>

<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Daftar Transaksi Pesanan Online</h4>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="table-responsive">
          <table class="table table-bordered">
            <thead class="bg-light">
              <tr>
                <th>Waktu & ID TRX</th>
                <th>Guest</th>
                <th>Detail Pesanan (Menu - Vendor)</th>
                <th>Total Nominal</th>
                <th>Status Pembayaran</th>
              </tr>
            </thead>
            <tbody>
              @forelse($pesanan as $p)
              <tr>
                <td>
                    <strong>{{ $p->timestamp }}</strong><br>
                    <small class="text-muted">{{ $p->midtrans_order_id }}</small>
                </td>
                <td>{{ $p->nama }}</td>
                <td>
                    <ul style="padding-left: 20px; margin-bottom:0;">
                    @foreach($p->details as $d)
                        <li>
                            @if($d->menu && $d->menu->vendor)
                                <strong>[{{ $d->menu->vendor->nama_vendor }}]</strong>
                            @endif
                            {{ $d->menu->nama_menu ?? 'Menu Terhapus' }} (x{{ $d->jumlah }})
                            @if($d->catatan)<br><small class="text-danger">Catatan: {{ $d->catatan }}</small>@endif
                        </li>
                    @endforeach
                    </ul>
                </td>
                <td class="font-weight-bold">Rp {{ number_format($p->total, 0, ',', '.') }}</td>
                <td>
                    @if($p->status_bayar == 1)
                        <span class="badge badge-success mb-2 d-inline-block"><i class="mdi mdi-check"></i> LUNAS</span>
                    @else
                        <span class="badge badge-warning mb-2 d-inline-block"><i class="mdi mdi-clock"></i> PENDING</span>
                    @endif
                    
                    <div class="mt-2">
                        <a href="{{ route('vendor.pesanan.sync', $p->idpesanan) }}" class="btn btn-xs btn-outline-info">
                            <i class="mdi mdi-refresh"></i> Sync Status
                        </a>
                        <button type="button" class="btn btn-xs btn-outline-success mt-1 btn-lihat-struk" data-id="{{ $p->idpesanan }}">
                            <i class="mdi mdi-qrcode"></i> Lihat Struk
                        </button>
                    </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="5" class="text-center">Belum ada transaksi pesanan sama sekali.</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
</div>

{{-- Modal Struk QR Code --}}
<div class="modal fade" id="modalStruk" tabindex="-1" role="dialog" aria-labelledby="modalStrukLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalStrukLabel"><i class="mdi mdi-qrcode"></i> Struk Pembayaran</h5>

      </div>
      <div class="modal-body text-center" id="modalStrukBody">
        <div id="strukLoading">
          <i class="mdi mdi-loading mdi-spin" style="font-size: 40px;"></i>
          <p class="mt-2">Memuat struk...</p>
        </div>
        <div id="strukContent" style="display:none;">
          <div class="mb-3">
            <img id="strukQrCode" src="" alt="QR Code" style="max-width: 200px;">
          </div>
          <div class="text-left p-3 border rounded bg-light">
            <p class="mb-1"><strong>ID Pesanan :</strong> <span id="strukIdPesanan"></span></p>
            <p class="mb-1"><strong>ID Order &nbsp;&nbsp;&nbsp;&nbsp;:</strong> <span id="strukOrderId" style="word-break:break-all;"></span></p>
            <p class="mb-1"><strong>Pemesan &nbsp;&nbsp;&nbsp;&nbsp;:</strong> <span id="strukNama"></span></p>
            <p class="mb-1"><strong>Total &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</strong> Rp <span id="strukTotal"></span></p>
            <p class="mb-0"><strong>Status &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</strong> <span id="strukStatus"></span></p>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-lihat-struk').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var idpesanan = this.dataset.id;

            // Reset modal
            document.getElementById('strukLoading').style.display = 'block';
            document.getElementById('strukContent').style.display = 'none';

            // Open modal
            var modalEl = document.getElementById('modalStruk');
            var modal = new bootstrap.Modal(modalEl);
            modal.show();

            // Fetch struk data via AJAX
            fetch('/vendor/pesanan/struk/' + idpesanan)
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    if (data.success) {
                        document.getElementById('strukQrCode').src = data.qr_code;
                        document.getElementById('strukIdPesanan').textContent = data.pesanan.idpesanan;
                        document.getElementById('strukOrderId').textContent = data.pesanan.midtrans_order_id;
                        document.getElementById('strukNama').textContent = data.pesanan.nama;
                        document.getElementById('strukTotal').textContent = data.pesanan.total;

                        var statusEl = document.getElementById('strukStatus');
                        if (data.pesanan.status_bayar == 1) {
                            statusEl.innerHTML = '<span class="badge badge-success">LUNAS</span>';
                        } else {
                            statusEl.innerHTML = '<span class="badge badge-warning text-dark">PENDING</span>';
                        }

                        document.getElementById('strukLoading').style.display = 'none';
                        document.getElementById('strukContent').style.display = 'block';
                    }
                })
                .catch(function(err) {
                    document.getElementById('strukLoading').innerHTML = '<p class="text-danger">Gagal memuat struk.</p>';
                    console.error(err);
                });
        });
    });
});
</script>
@endpush
