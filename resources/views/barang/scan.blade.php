@extends('layouts.master')

@section('content')
<div class="page-header">
  <h3 class="page-title">
    <span class="page-title-icon bg-gradient-info text-white me-2">
      <i class="mdi mdi-barcode-scan"></i>
    </span> Barcode Scanner
  </h3>
  <nav aria-label="breadcrumb">
    <a href="{{ route('barang.index') }}" class="btn btn-gradient-primary btn-fw mb-2">
      <i class="mdi mdi-arrow-left"></i> Kembali ke Data Barang
    </a>
  </nav>
</div>

<div class="row justify-content-center">
  {{-- Scanner Area --}}
  <div class="col-md-6 grid-margin stretch-card">
    <div class="card">
      <div class="card-body text-center">
        <h4 class="card-title"><i class="mdi mdi-camera"></i> Arahkan Kamera ke Barcode</h4>
        <div id="reader" style="width: 100%; max-width: 500px; margin: 0 auto;"></div>
        <button id="btnScanUlang" class="btn btn-gradient-info btn-fw mt-3" style="display: none;">
          <i class="mdi mdi-restart"></i> Scan Ulang
        </button>
      </div>
    </div>
  </div>

  {{-- Hasil Scan --}}
  <div class="col-md-6 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title"><i class="mdi mdi-package-variant"></i> Hasil Scan Barang</h4>

        <div id="hasilScanPlaceholder" class="text-center text-muted py-5">
          <i class="mdi mdi-barcode" style="font-size: 60px; opacity: 0.3;"></i>
          <p class="mt-2">Belum ada barcode yang di-scan.<br>Arahkan kamera ke kertas label barcode.</p>
        </div>

        <div id="hasilScanLoading" class="text-center py-5" style="display: none;">
          <i class="mdi mdi-loading mdi-spin" style="font-size: 40px;"></i>
          <p class="mt-2">Memuat data barang...</p>
        </div>

        <div id="hasilScanData" style="display: none;">
          <div class="table-responsive">
            <table class="table table-bordered">
              <tbody>
                <tr>
                  <th class="bg-light" width="35%">ID Barang</th>
                  <td><strong id="resultIdBarang" class="text-primary"></strong></td>
                </tr>
                <tr>
                  <th class="bg-light">Nama Barang</th>
                  <td id="resultNama"></td>
                </tr>
                <tr>
                  <th class="bg-light">Harga</th>
                  <td><strong id="resultHarga" class="text-success"></strong></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div id="hasilScanError" class="alert alert-danger" style="display: none;">
          <i class="mdi mdi-alert-circle"></i> <span id="errorMessage"></span>
        </div>

      </div>
    </div>
  </div>
</div>

{{-- Riwayat Scan --}}
<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title"><i class="mdi mdi-history"></i> Riwayat Scan</h4>
        <div class="table-responsive">
          <table class="table table-hover table-sm" id="tabelRiwayat">
            <thead>
              <tr>
                <th>#</th>
                <th>ID Barang</th>
                <th>Nama Barang</th>
                <th>Harga</th>
                <th>Waktu Scan</th>
              </tr>
            </thead>
            <tbody>
              {{-- Diisi secara dinamis --}}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ==========================================
    // Web Audio API – Beep Sound Generator
    // ==========================================
    const AudioContext = window.AudioContext || window.webkitAudioContext;
    let audioCtx = null;

    function playBeep(frequency = 1800, duration = 150) {
        if (!audioCtx) {
            audioCtx = new AudioContext();
        }
        const oscillator = audioCtx.createOscillator();
        const gainNode = audioCtx.createGain();
        oscillator.connect(gainNode);
        gainNode.connect(audioCtx.destination);
        oscillator.type = 'square';
        oscillator.frequency.setValueAtTime(frequency, audioCtx.currentTime);
        gainNode.gain.setValueAtTime(1.0, audioCtx.currentTime); // Volume maksimal
        oscillator.start();
        oscillator.stop(audioCtx.currentTime + duration / 1000);
    }

    const html5QrCode = new Html5Qrcode("reader");
    let isScanning = false;
    let scanCounter = 0;

    const config = {
        fps: 10,
        qrbox: { width: 300, height: 100 },
        formatsToSupport: [
            Html5QrcodeSupportedFormats.CODE_128,
            Html5QrcodeSupportedFormats.CODE_39,
            Html5QrcodeSupportedFormats.EAN_13,
            Html5QrcodeSupportedFormats.EAN_8,
            Html5QrcodeSupportedFormats.UPC_A,
            Html5QrcodeSupportedFormats.UPC_E,
            Html5QrcodeSupportedFormats.QR_CODE,
        ]
    };

    function startScanner() {
        // Reset UI
        document.getElementById('hasilScanPlaceholder').style.display = 'block';
        document.getElementById('hasilScanLoading').style.display = 'none';
        document.getElementById('hasilScanData').style.display = 'none';
        document.getElementById('hasilScanError').style.display = 'none';
        document.getElementById('btnScanUlang').style.display = 'none';

        html5QrCode.start(
            { facingMode: "environment" },
            config,
            onScanSuccess,
            onScanFailure
        ).then(() => {
            isScanning = true;
        }).catch(err => {
            console.error('Gagal membuka kamera:', err);
            document.getElementById('hasilScanPlaceholder').innerHTML = 
                '<i class="mdi mdi-camera-off" style="font-size: 60px; color: #e74c3c;"></i>' +
                '<p class="mt-2 text-danger">Gagal membuka kamera.<br>Pastikan izin kamera sudah diberikan.</p>';
        });
    }

    function stopScanner() {
        if (isScanning) {
            html5QrCode.stop().then(() => {
                isScanning = false;
                document.getElementById('btnScanUlang').style.display = 'inline-block';
            }).catch(err => console.error('Stop error:', err));
        }
    }

    function onScanSuccess(decodedText, decodedResult) {
        // 1. Beep!
        playBeep();

        // 2. Stop scanning
        stopScanner();

        // 3. Fetch data barang
        fetchBarangData(decodedText.trim());
    }

    function onScanFailure(error) {
        // Silence – normal when no barcode detected
    }

    function fetchBarangData(idBarang) {
        document.getElementById('hasilScanPlaceholder').style.display = 'none';
        document.getElementById('hasilScanLoading').style.display = 'block';
        document.getElementById('hasilScanData').style.display = 'none';
        document.getElementById('hasilScanError').style.display = 'none';

        fetch('/barang/api/scan/' + encodeURIComponent(idBarang))
            .then(res => res.json())
            .then(data => {
                document.getElementById('hasilScanLoading').style.display = 'none';

                if (data.success) {
                    document.getElementById('resultIdBarang').textContent = data.data.id_barang;
                    document.getElementById('resultNama').textContent = data.data.nama;
                    document.getElementById('resultHarga').textContent = data.data.harga_format;
                    document.getElementById('hasilScanData').style.display = 'block';

                    // Tambah ke riwayat
                    addToRiwayat(data.data);
                } else {
                    document.getElementById('errorMessage').textContent = data.message || 'Barang tidak ditemukan.';
                    document.getElementById('hasilScanError').style.display = 'block';
                }
            })
            .catch(err => {
                document.getElementById('hasilScanLoading').style.display = 'none';
                document.getElementById('errorMessage').textContent = 'Gagal memuat data: ' + err.message;
                document.getElementById('hasilScanError').style.display = 'block';
            });
    }

    function addToRiwayat(barang) {
        scanCounter++;
        const tbody = document.querySelector('#tabelRiwayat tbody');
        const tr = document.createElement('tr');
        const now = new Date().toLocaleString('id-ID');
        tr.innerHTML = `
            <td>${scanCounter}</td>
            <td><strong>${barang.id_barang}</strong></td>
            <td>${barang.nama}</td>
            <td>${barang.harga_format}</td>
            <td>${now}</td>
        `;
        // Sisipkan di baris paling atas
        tbody.insertBefore(tr, tbody.firstChild);
    }

    // Tombol Scan Ulang
    document.getElementById('btnScanUlang').addEventListener('click', function() {
        startScanner();
    });

    // Mulai scanner saat halaman dimuat
    startScanner();
});
</script>
@endpush
