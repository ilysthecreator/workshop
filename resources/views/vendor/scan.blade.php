@extends('layouts.master')

@section('content')
<div class="page-header">
  <h3 class="page-title">
    <span class="page-title-icon bg-gradient-warning text-white me-2">
      <i class="mdi mdi-qrcode-scan"></i>
    </span> Scan QR Pesanan Customer
  </h3>
  <nav aria-label="breadcrumb">
    <a href="{{ route('vendor.pesanan') }}" class="btn btn-gradient-primary btn-fw mb-2">
      <i class="mdi mdi-arrow-left"></i> Kembali ke Data Pesanan
    </a>
  </nav>
</div>

<div class="row justify-content-center">
  {{-- Scanner Area --}}
  <div class="col-md-5 grid-margin stretch-card">
    <div class="card">
      <div class="card-body text-center">
        <h4 class="card-title"><i class="mdi mdi-camera"></i> Arahkan Kamera ke QR Code</h4>
        <p class="text-muted mb-3">Scan QR Code dari halaman pembayaran customer</p>
        <div id="reader" style="width: 100%; max-width: 400px; margin: 0 auto;"></div>
        <button id="btnScanUlang" class="btn btn-gradient-warning btn-fw mt-3" style="display: none;">
          <i class="mdi mdi-restart"></i> Scan Ulang
        </button>
      </div>
    </div>
  </div>

  {{-- Hasil Scan --}}
  <div class="col-md-7 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title"><i class="mdi mdi-clipboard-text"></i> Detail Pesanan</h4>

        <div id="hasilScanPlaceholder" class="text-center text-muted py-5">
          <i class="mdi mdi-qrcode" style="font-size: 60px; opacity: 0.3;"></i>
          <p class="mt-2">Belum ada QR Code yang di-scan.<br>Arahkan kamera ke QR Code pesanan customer.</p>
        </div>

        <div id="hasilScanLoading" class="text-center py-5" style="display: none;">
          <i class="mdi mdi-loading mdi-spin" style="font-size: 40px;"></i>
          <p class="mt-2">Memuat data pesanan...</p>
        </div>

        <div id="hasilScanData" style="display: none;">
          {{-- Info Pesanan --}}
          <div class="p-3 border rounded bg-light mb-3">
            <div class="row">
              <div class="col-6">
                <p class="mb-1"><strong>ID Pesanan:</strong></p>
                <h4 id="resultIdPesanan" class="text-primary font-weight-bold"></h4>
              </div>
              <div class="col-6 text-right">
                <p class="mb-1"><strong>Status Bayar:</strong></p>
                <span id="resultStatusBadge"></span>
              </div>
            </div>
            <hr>
            <p class="mb-1"><small class="text-muted"><strong>Pemesan:</strong> <span id="resultNama"></span></small></p>
            <p class="mb-1"><small class="text-muted"><strong>Total:</strong> <span id="resultTotal" class="font-weight-bold"></span></small></p>
            <p class="mb-0"><small class="text-muted"><strong>Waktu:</strong> <span id="resultTimestamp"></span></small></p>
          </div>

          {{-- Tabel Menu Dipesan --}}
          <h5 class="font-weight-bold mb-2"><i class="mdi mdi-food-fork-drink"></i> Menu yang Dipesan</h5>
          <div class="table-responsive">
            <table class="table table-bordered table-sm">
              <thead class="thead-dark">
                <tr>
                  <th>Menu</th>
                  <th>Vendor</th>
                  <th class="text-center">Qty</th>
                  <th>Harga</th>
                  <th>Subtotal</th>
                  <th>Catatan</th>
                </tr>
              </thead>
              <tbody id="tabelMenuBody">
                {{-- Filled dynamically --}}
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

    // ==========================================
    // HTML5 QR Code Scanner
    // ==========================================
    const html5QrCode = new Html5Qrcode("reader");
    let isScanning = false;

    const config = {
        fps: 10,
        qrbox: { width: 250, height: 250 },
        formatsToSupport: [
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

        // 3. Ambil idpesanan dari QR Code
        let idpesanan = decodedText.trim();

        // 4. Fetch data pesanan
        fetchPesananData(idpesanan);
    }

    function onScanFailure(error) {
        // Silence – normal when no QR detected
    }

    function fetchPesananData(idpesanan) {
        document.getElementById('hasilScanPlaceholder').style.display = 'none';
        document.getElementById('hasilScanLoading').style.display = 'block';
        document.getElementById('hasilScanData').style.display = 'none';
        document.getElementById('hasilScanError').style.display = 'none';

        fetch('/vendor/scan/api/' + encodeURIComponent(idpesanan))
            .then(res => res.json())
            .then(data => {
                document.getElementById('hasilScanLoading').style.display = 'none';

                if (data.success) {
                    // Isi info pesanan
                    document.getElementById('resultIdPesanan').textContent = data.pesanan.idpesanan;
                    document.getElementById('resultNama').textContent = data.pesanan.nama;
                    document.getElementById('resultTotal').textContent = data.pesanan.total_format;
                    document.getElementById('resultTimestamp').textContent = data.pesanan.timestamp || '-';

                    // Status Badge
                    const statusEl = document.getElementById('resultStatusBadge');
                    if (data.pesanan.status_bayar == 1) {
                        statusEl.innerHTML = '<span class="badge badge-success" style="font-size: 16px; padding: 8px 16px;"><i class="mdi mdi-check-circle"></i> LUNAS</span>';
                    } else {
                        statusEl.innerHTML = '<span class="badge badge-warning text-dark" style="font-size: 16px; padding: 8px 16px;"><i class="mdi mdi-clock"></i> BELUM LUNAS</span>';
                    }

                    // Isi tabel menu
                    const tbody = document.getElementById('tabelMenuBody');
                    tbody.innerHTML = '';
                    data.items.forEach(function(item) {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td><strong>${item.nama_menu}</strong></td>
                            <td>${item.vendor}</td>
                            <td class="text-center">${item.jumlah}</td>
                            <td>${item.harga_format}</td>
                            <td><strong>${item.subtotal_format}</strong></td>
                            <td>${item.catatan || '-'}</td>
                        `;
                        tbody.appendChild(tr);
                    });

                    document.getElementById('hasilScanData').style.display = 'block';
                } else {
                    document.getElementById('errorMessage').textContent = data.message || 'Pesanan tidak ditemukan.';
                    document.getElementById('hasilScanError').style.display = 'block';
                }
            })
            .catch(err => {
                document.getElementById('hasilScanLoading').style.display = 'none';
                document.getElementById('errorMessage').textContent = 'Gagal memuat data: ' + err.message;
                document.getElementById('hasilScanError').style.display = 'block';
            });
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
