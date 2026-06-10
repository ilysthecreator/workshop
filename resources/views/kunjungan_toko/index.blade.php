@extends('layouts.master')

@section('content')
<div class="page-header">
  <h3 class="page-title">
    <span class="page-title-icon bg-gradient-primary text-white me-2">
      <i class="mdi mdi-map-marker-radius"></i>
    </span> List Toko
  </h3>
  <nav aria-label="breadcrumb">
    <div class="d-flex justify-content-between">
      <a href="{{ route('kunjungan-toko.create') }}" class="btn btn-gradient-primary btn-fw">
        + Input Titik Awal
      </a>
    </div>
  </nav>
</div>

<div class="row">
  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        
        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        <h4 class="card-title">Daftar Lokasi Toko</h4>
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Barcode</th>
                <th>Nama Toko</th>
                <th>Latitude</th>
                <th>Longitude</th>
                <th>Accuracy</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach($tokos as $toko)
              <tr>
                <td>{{ $toko->barcode }}</td>
                <td>{{ $toko->nama_toko }}</td>
                <td>{{ $toko->latitude }}</td>
                <td>{{ $toko->longitude }}</td>
                <td>{{ $toko->accuracy }} m</td>
                <td>
                    <!-- Modal Print Barcode -->
                    <button type="button" class="btn btn-sm btn-gradient-info" data-bs-toggle="modal" data-bs-target="#modalPrint{{ $toko->barcode }}">
                        Cetak Barcode
                    </button>

                    <div class="modal fade" id="modalPrint{{ $toko->barcode }}" tabindex="-1" aria-labelledby="modalLabel{{ $toko->barcode }}" aria-hidden="true">
                      <div class="modal-dialog modal-sm">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="modalLabel{{ $toko->barcode }}">Barcode Toko: {{ $toko->nama_toko }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body text-center" id="printArea{{ $toko->barcode }}">
                            @php
                                $generator = new Picqer\Barcode\BarcodeGeneratorSVG();
                            @endphp
                            <div style="margin: 0 auto; display: flex; justify-content: center; margin-bottom: 15px;">
                                {!! $generator->getBarcode($toko->barcode, $generator::TYPE_CODE_128, 2, 50) !!}
                            </div>
                            <p class="mb-0" style="font-size: 18px;"><strong>{{ $toko->barcode }}</strong></p>
                            <p class="mb-0" style="font-size: 14px;">{{ $toko->nama_toko }}</p>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="button" class="btn btn-primary" onclick="printBarcode('{{ $toko->barcode }}')">Print</button>
                          </div>
                        </div>
                      </div>
                    </div>
                </td>
              </tr>
              @endforeach
              @if($tokos->isEmpty())
              <tr>
                <td colspan="6" class="text-center">Belum ada data toko.</td>
              </tr>
              @endif
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function printBarcode(barcodeId) {
    var printContent = document.getElementById('printArea' + barcodeId).innerHTML;
    var originalContent = document.body.innerHTML;

    document.body.innerHTML = printContent;
    window.print();
    document.body.innerHTML = originalContent;
    location.reload(); // reload to reattach event listeners
}
</script>
@endsection
