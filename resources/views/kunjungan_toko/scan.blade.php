@extends('layouts.master')

@section('content')
<div class="page-header">
  <h3 class="page-title">
    <span class="page-title-icon bg-gradient-info text-white me-2">
      <i class="mdi mdi-camera-metering-center"></i>
    </span> Titik Kunjungan (Sales)
  </h3>
  <nav aria-label="breadcrumb">
    <a href="{{ route('kunjungan-toko.index') }}" class="btn btn-gradient-primary btn-fw mb-2">
      <i class="mdi mdi-arrow-left"></i> List Toko
    </a>
  </nav>
</div>

<div class="row justify-content-center">
  {{-- Scanner Area --}}
  <div class="col-md-5 grid-margin stretch-card">
    <div class="card">
      <div class="card-body text-center">
        <h4 class="card-title"><i class="mdi mdi-camera"></i> Barcode Scanner</h4>
        <div id="reader" style="width: 100%; max-width: 500px; margin: 0 auto;"></div>
        <button id="btnScanUlang" class="btn btn-gradient-info btn-fw mt-3" style="display: none;">
          <i class="mdi mdi-restart"></i> Scan Ulang
        </button>
      </div>
    </div>
  </div>

  {{-- Hasil Scan & Verifikasi --}}
  <div class="col-md-7 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title"><i class="mdi mdi-map-marker-distance"></i> Verifikasi Kunjungan</h4>

        {{-- Placeholder awal --}}
        <div id="hasilScanPlaceholder" class="text-center text-muted py-5">
          <i class="mdi mdi-qrcode-scan" style="font-size: 60px; opacity: 0.3;"></i>
          <p class="mt-2">Belum ada barcode toko yang di-scan.</p>
        </div>

        {{-- Loading saat fetch data DB --}}
        <div id="statusLoading" class="text-center py-4" style="display: none;">
          <i class="mdi mdi-loading mdi-spin text-primary" style="font-size: 40px;"></i>
          <p class="mt-2" id="textLoading">Memuat data toko...</p>
        </div>

        {{-- Data dari DB hasil scan barcode --}}
        <div id="dataTokoFromDB" style="display: none;">
          <h5 class="mb-3"><i class="mdi mdi-database"></i> Data dari DB hasil scan barcode</h5>
          <div class="table-responsive">
            <table class="table table-bordered">
              <tbody>
                <tr>
                  <th class="bg-light" width="40%">Barcode</th>
                  <td><strong id="dbBarcode" class="text-primary"></strong></td>
                </tr>
                <tr>
                  <th class="bg-light">Nama Toko</th>
                  <td id="dbNamaToko"></td>
                </tr>
                <tr>
                  <th class="bg-light">Latitude</th>
                  <td id="dbLatitude"></td>
                </tr>
                <tr>
                  <th class="bg-light">Longitude</th>
                  <td id="dbLongitude"></td>
                </tr>
                <tr>
                  <th class="bg-light">Accuracy</th>
                  <td id="dbAccuracy"></td>
                </tr>
              </tbody>
            </table>
          </div>

          <hr>

          {{-- Data titik kunjungan (hasil verifikasi) --}}
          <div id="dataKunjungan" style="display: none;">
            <h5 class="mb-3"><i class="mdi mdi-map-marker-check"></i> Data Titik Kunjungan</h5>
            <div class="table-responsive">
              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th class="bg-light" width="40%">Latitude Sales</th>
                    <td id="salesLatitude"></td>
                  </tr>
                  <tr>
                    <th class="bg-light">Longitude Sales</th>
                    <td id="salesLongitude"></td>
                  </tr>
                  <tr>
                    <th class="bg-light">Accuracy Sales</th>
                    <td id="salesAccuracy"></td>
                  </tr>
                  <tr>
                    <th class="bg-light">Jarak Aktual</th>
                    <td id="resJarakAktual"></td>
                  </tr>
                  <tr>
                    <th class="bg-light">Threshold Efektif</th>
                    <td id="resThreshold"></td>
                  </tr>
                  <tr>
                    <th class="bg-light">Status Kunjungan</th>
                    <td>
                        <h4 id="resStatus" class="mb-0"></h4>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          {{-- Loading saat ambil lokasi --}}
          <div id="statusAmbilLokasi" class="text-center py-3" style="display: none;">
            <i class="mdi mdi-loading mdi-spin text-info" style="font-size: 30px;"></i>
            <p class="mt-2">Sedang mengambil lokasi dengan akurasi tinggi...</p>
          </div>

          {{-- Tombol Ambil Lokasi --}}
          <div class="text-end mt-3" id="wrapperBtnAmbilLokasi">
            <button type="button" class="btn btn-gradient-success btn-lg" id="btnAmbilLokasi">
              <i class="mdi mdi-crosshairs-gps"></i> Ambil Lokasi
            </button>
          </div>
        </div>

        {{-- Error --}}
        <div id="hasilError" class="alert alert-danger mt-3" style="display: none;">
          <i class="mdi mdi-alert-circle"></i> <span id="errorMessage"></span>
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
    // Web Audio API – Beep Sound
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
        gainNode.gain.setValueAtTime(1.0, audioCtx.currentTime);
        oscillator.start();
        oscillator.stop(audioCtx.currentTime + duration / 1000);
    }

    const html5QrCode = new Html5Qrcode("reader");
    let isScanning = false;
    let scannedBarcode = null; // barcode yang sudah di-scan, dipakai saat "Ambil Lokasi"

    const config = {
        fps: 10,
        qrbox: { width: 250, height: 100 },
        formatsToSupport: [
            Html5QrcodeSupportedFormats.CODE_128,
            Html5QrcodeSupportedFormats.CODE_39,
            Html5QrcodeSupportedFormats.EAN_13,
            Html5QrcodeSupportedFormats.QR_CODE,
        ]
    };

    function startScanner() {
        // Reset semua UI
        scannedBarcode = null;
        document.getElementById('hasilScanPlaceholder').style.display = 'block';
        document.getElementById('statusLoading').style.display = 'none';
        document.getElementById('dataTokoFromDB').style.display = 'none';
        document.getElementById('dataKunjungan').style.display = 'none';
        document.getElementById('statusAmbilLokasi').style.display = 'none';
        document.getElementById('hasilError').style.display = 'none';
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

    // === STEP 1: Scan barcode → fetch data toko dari DB ===
    function onScanSuccess(decodedText, decodedResult) {
        playBeep();
        stopScanner();

        scannedBarcode = decodedText.trim();

        // Tampilkan loading
        document.getElementById('hasilScanPlaceholder').style.display = 'none';
        document.getElementById('statusLoading').style.display = 'block';
        document.getElementById('hasilError').style.display = 'none';

        // Fetch data toko dari DB via API (GET, hanya ambil data toko)
        fetch('{{ route("kunjungan-toko.api-scan") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                barcode: scannedBarcode,
                mode: 'lookup' // hanya lookup data toko, belum verifikasi
            })
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('statusLoading').style.display = 'none';

            if (data.status === 'success' || data.status === 'lookup') {
                // Tampilkan data toko dari DB
                const toko = data.toko;
                document.getElementById('dbBarcode').textContent = toko.barcode;
                document.getElementById('dbNamaToko').textContent = toko.nama_toko;
                document.getElementById('dbLatitude').textContent = toko.latitude;
                document.getElementById('dbLongitude').textContent = toko.longitude;
                document.getElementById('dbAccuracy').textContent = toko.accuracy + ' m';
                
                // Tampilkan panel dan tombol Ambil Lokasi
                document.getElementById('dataTokoFromDB').style.display = 'block';
                document.getElementById('dataKunjungan').style.display = 'none';
                document.getElementById('statusAmbilLokasi').style.display = 'none';
                document.getElementById('wrapperBtnAmbilLokasi').style.display = 'block';
            } else {
                document.getElementById('errorMessage').textContent = data.message || 'Toko tidak ditemukan.';
                document.getElementById('hasilError').style.display = 'block';
            }
        })
        .catch(err => {
            document.getElementById('statusLoading').style.display = 'none';
            document.getElementById('errorMessage').textContent = 'Gagal memuat data: ' + err.message;
            document.getElementById('hasilError').style.display = 'block';
        });
    }

    function onScanFailure(error) {
        // Abaikan
    }

    // === STEP 2: Klik "Ambil Lokasi" → ambil GPS sales → verifikasi jarak ===
    document.getElementById('btnAmbilLokasi').addEventListener('click', async function() {
        document.getElementById('wrapperBtnAmbilLokasi').style.display = 'none';
        document.getElementById('statusAmbilLokasi').style.display = 'block';
        document.getElementById('dataKunjungan').style.display = 'none';

        try {
            const pos = await getAccuratePosition(50); // target accuracy <= 50m
            const salesLat = pos.coords.latitude;
            const salesLng = pos.coords.longitude;
            const salesAcc = pos.coords.accuracy;

            document.getElementById('statusAmbilLokasi').style.display = 'none';

            // Kirim ke API untuk verifikasi jarak
            verifyVisit(scannedBarcode, salesLat, salesLng, salesAcc);

        } catch (err) {
            document.getElementById('statusAmbilLokasi').style.display = 'none';
            document.getElementById('wrapperBtnAmbilLokasi').style.display = 'block';
            document.getElementById('errorMessage').textContent = 'Gagal mendapatkan lokasi: ' + err.message;
            document.getElementById('hasilError').style.display = 'block';
        }
    });

    function verifyVisit(barcode, lat, lng, acc) {
        fetch('{{ route("kunjungan-toko.api-scan") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                barcode: barcode,
                latitude: lat,
                longitude: lng,
                accuracy: acc,
                mode: 'verify'
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                // Tampilkan data sales
                document.getElementById('salesLatitude').textContent = lat;
                document.getElementById('salesLongitude').textContent = lng;
                document.getElementById('salesAccuracy').textContent = acc.toFixed(2) + ' m';
                document.getElementById('resJarakAktual').textContent = data.jarak_aktual + ' meter';
                document.getElementById('resThreshold').textContent = data.threshold_efektif + ' meter';

                const statusEl = document.getElementById('resStatus');
                if (data.is_accepted) {
                    statusEl.innerHTML = '<span class="badge badge-success px-4 py-2" style="font-size: 16px;"><i class="mdi mdi-check-circle"></i> DITERIMA ✓</span>';
                } else {
                    statusEl.innerHTML = '<span class="badge badge-danger px-4 py-2" style="font-size: 16px;"><i class="mdi mdi-close-circle"></i> DITOLAK ✗</span>';
                }

                document.getElementById('dataKunjungan').style.display = 'block';
            } else {
                document.getElementById('errorMessage').textContent = data.message || 'Toko tidak ditemukan.';
                document.getElementById('hasilError').style.display = 'block';
            }
        })
        .catch(err => {
            document.getElementById('errorMessage').textContent = 'Terjadi kesalahan server: ' + err.message;
            document.getElementById('hasilError').style.display = 'block';
        });
    }

    // Fungsi getAccuratePosition (Lampiran 1)
    function getAccuratePosition(targetAccuracy = 50, maxWait = 20000) {
        return new Promise((resolve, reject) => {
            let bestResult = null;
            const startTime = Date.now();

            const watchId = navigator.geolocation.watchPosition(
                (position) => {
                    const acc = position.coords.accuracy;

                    if (!bestResult || acc < bestResult.coords.accuracy) {
                        bestResult = position;
                    }

                    if (acc <= targetAccuracy) {
                        navigator.geolocation.clearWatch(watchId);
                        resolve(bestResult);
                    }

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

    document.getElementById('btnScanUlang').addEventListener('click', function() {
        startScanner();
    });

    startScanner();
});
</script>
@endpush
