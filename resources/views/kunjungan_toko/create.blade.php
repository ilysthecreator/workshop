@extends('layouts.master')

@section('content')
<div class="page-header">
  <h3 class="page-title">
    <span class="page-title-icon bg-gradient-primary text-white me-2">
      <i class="mdi mdi-map-marker-plus"></i>
    </span> Input Titik Awal Toko
  </h3>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('kunjungan-toko.index') }}">List Toko</a></li>
      <li class="breadcrumb-item active" aria-current="page">Input Titik Awal</li>
    </ol>
  </nav>
</div>

<div class="row">
  <div class="col-md-6 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Form Input Titik Awal</h4>
        <p class="card-description"> Masukkan data toko dan ambil koordinat lokasinya </p>
        
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form class="forms-sample" method="POST" action="{{ route('kunjungan-toko.store') }}">
          @csrf
          <div class="form-group">
            <label for="barcode">Barcode</label>
            <input type="text" class="form-control" id="barcode" name="barcode" placeholder="Barcode (Max 8 Char)" maxlength="8" required value="{{ old('barcode') }}">
          </div>
          <div class="form-group">
            <label for="nama_toko">Nama Toko</label>
            <input type="text" class="form-control" id="nama_toko" name="nama_toko" placeholder="Nama Toko" required value="{{ old('nama_toko') }}">
          </div>
          
          <div class="form-group">
            <label for="latitude">Latitude</label>
            <input type="text" class="form-control" id="latitude" name="latitude" placeholder="Latitude" readonly required value="{{ old('latitude') }}">
          </div>
          <div class="form-group">
            <label for="longitude">Longitude</label>
            <input type="text" class="form-control" id="longitude" name="longitude" placeholder="Longitude" readonly required value="{{ old('longitude') }}">
          </div>
          <div class="form-group">
            <label for="accuracy">Accuracy (meters)</label>
            <input type="text" class="form-control" id="accuracy" name="accuracy" placeholder="Accuracy" readonly required value="{{ old('accuracy') }}">
          </div>

          <div class="d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-gradient-info me-2" id="btnGeoloc">
                <i class="mdi mdi-crosshairs-gps"></i> Geoloc
            </button>
            <button type="submit" class="btn btn-gradient-primary me-2">Submit</button>
          </div>
          <div class="mt-2 text-center" id="geolocStatus" style="display: none;">
            <small class="text-muted"><i class="mdi mdi-loading mdi-spin"></i> Sedang mengambil lokasi paling akurat...</small>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.getElementById('btnGeoloc').addEventListener('click', async function() {
    const statusEl = document.getElementById('geolocStatus');
    statusEl.style.display = 'block';
    statusEl.innerHTML = '<small class="text-muted"><i class="mdi mdi-loading mdi-spin"></i> Sedang mengambil lokasi paling akurat...</small>';

    try {
        const pos = await getAccuratePosition(50); // target accuracy <= 50m
        
        document.getElementById('latitude').value = pos.coords.latitude;
        document.getElementById('longitude').value = pos.coords.longitude;
        document.getElementById('accuracy').value = pos.coords.accuracy;
        
        statusEl.innerHTML = '<small class="text-success"><i class="mdi mdi-check"></i> Lokasi berhasil didapatkan (Akurasi: ' + pos.coords.accuracy.toFixed(2) + 'm)</small>';
    } catch (err) {
        alert("Gagal mendapatkan lokasi: " + err.message);
        statusEl.innerHTML = '<small class="text-danger"><i class="mdi mdi-close"></i> Gagal mendapatkan lokasi</small>';
    }
});

function getAccuratePosition(targetAccuracy = 50, maxWait = 20000) {
    return new Promise((resolve, reject) => {
        let bestResult = null;
        const startTime = Date.now();

        const watchId = navigator.geolocation.watchPosition(
            (position) => {
                const acc = position.coords.accuracy;

                // Simpan hasil terbaik sejauh ini
                if (!bestResult || acc < bestResult.coords.accuracy) {
                    bestResult = position;
                }

                // Kalau sudah cukup akurat, berhenti
                if (acc <= targetAccuracy) {
                    navigator.geolocation.clearWatch(watchId);
                    resolve(bestResult);
                }

                // Kalau timeout, pakai hasil terbaik yang ada
                if (Date.now() - startTime >= maxWait) {
                    navigator.geolocation.clearWatch(watchId);
                    if (bestResult) resolve(bestResult);
                    else reject(new Error("Timeout, tidak dapat posisi"));
                }
            },
            (error) => reject(error),
            { enableHighAccuracy: true, maximumAge: 0, timeout: maxWait }
        );
    });
}
</script>
@endsection
