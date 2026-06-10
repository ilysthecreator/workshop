@extends('layouts.master')

@section('content')
<div class="page-header">
  <h3 class="page-title">
    <span class="page-title-icon bg-gradient-primary text-white me-2">
      <i class="mdi mdi-nfc"></i>
    </span> Mobile Scanner Absensi
  </h3>
  <nav aria-label="breadcrumb">
    <ul class="breadcrumb">
      <li class="breadcrumb-item active" aria-current="page">
        <span></span>Scanner Device <i class="mdi mdi-cellphone-nfc icon-sm text-primary align-middle"></i>
      </li>
    </ul>
  </nav>
</div>

<div class="row justify-content-center">
  <div class="col-md-6 col-lg-5 grid-margin stretch-card">
    <div class="card card-scanner-container text-white bg-dark-glow">
      <div class="card-body p-4 text-center d-flex flex-column align-items-center justify-content-center" style="min-height: 480px;">
        
        <h3 class="font-weight-bold mb-1 text-gradient-purple">NFC ABSENSI SCANNER</h3>
        <p class="text-muted small mb-4">Gunakan smartphone Android Anda sebagai scanner absensi</p>

        <!-- Radar Animasi Premium -->
        <div class="radar-container my-4" id="radarContainer">
          <div class="radar-ring circle-1"></div>
          <div class="radar-ring circle-2"></div>
          <div class="radar-ring circle-3"></div>
          <div class="radar-core">
            <i class="mdi mdi-nfc-tap text-white" id="radarIcon" style="font-size: 60px;"></i>
          </div>
        </div>

        <!-- Tombol Scan NFC (User Gesture Required) -->
        <div id="actionContainer" class="mt-3 w-100">
          <button type="button" class="btn btn-gradient-primary btn-lg w-100 py-3 font-weight-bold fs-5 text-uppercase" id="btnActivateScanner" style="border-radius: 30px; letter-spacing: 1px; box-shadow: 0 4px 15px rgba(185, 107, 241, 0.4);">
            <i class="mdi mdi-power"></i> Aktifkan Sensor NFC
          </button>
        </div>

        <!-- Status Card Real-time -->
        <div class="status-panel mt-4 w-100" id="statusPanel">
          <div class="badge badge-outline-light py-2 px-3 mb-2" id="scannerStatusText" style="border-radius: 15px; font-size: 13px;">Sensor NFC Belum Aktif</div>
          <p class="text-muted small" id="scannerSubtext">Klik tombol di atas untuk mulai memindai kartu mahasiswa.</p>
        </div>

        <!-- Hasil Pemindaian Absensi Terakhir -->
        <div id="attendanceResultCard" class="mt-4 w-100 d-none">
          <div class="card bg-gradient-success border-0 text-white p-3 text-start attendance-success-box">
            <div class="d-flex align-items-center mb-2">
              <i class="mdi mdi-checkbox-marked-circle-outline me-2 fs-4"></i>
              <strong class="fs-5" id="resultStatusText">Absensi Berhasil!</strong>
            </div>
            <div class="ps-1">
              <h4 class="font-weight-bold mb-1" id="resStudentName">-</h4>
              <p class="mb-1 small" id="resStudentNim">-</p>
              <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top border-white-50">
                <span class="badge bg-white text-success font-weight-bold px-2 py-1" id="resStudentClass">-</span>
                <span class="small" id="resTappedTime">00:00:00</span>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Panel Simulasi & Petunjuk -->
  <div class="col-md-6 col-lg-5 grid-margin stretch-card">
    <div class="card">
      <div class="card-body d-flex flex-column justify-content-between">
        <div>
          <h4 class="card-title text-primary"><i class="mdi mdi-help-circle-outline"></i> Panduan Penggunaan ngrok</h4>
          <p class="text-muted small">
            Web NFC API **hanya** didukung pada browser Google Chrome di Android dan memerlukan protokol **HTTPS** yang aman.
          </p>
          <hr>
          
          <div class="guide-steps small">
            <div class="d-flex align-items-start mb-2">
              <span class="badge badge-primary me-2">1</span>
              <div>Jalankan server Laravel lokal Anda: <br><code>php artisan serve --port=8000</code></div>
            </div>
            <div class="d-flex align-items-start mb-2">
              <span class="badge badge-primary me-2">2</span>
              <div>Hubungkan ngrok ke port server: <br><code>ngrok http 8000</code></div>
            </div>
            <div class="d-flex align-items-start mb-2">
              <span class="badge badge-primary me-2">3</span>
              <div>Buka URL HTTPS ngrok (contoh: <code>https://xxx.ngrok-free.app/nfc-absensi/scan</code>) di HP Android Anda.</div>
            </div>
            <div class="d-flex align-items-start">
              <span class="badge badge-primary me-2">4</span>
              <div>Klik **Aktifkan Sensor NFC** dan dekatkan kartu ke HP untuk melakukan absensi secara instan!</div>
            </div>
          </div>
        </div>

        <!-- Mode Simulasi di Desktop -->
        <div class="mt-4 pt-3 border-top" id="mockScanSection">
          <h5 class="text-warning font-weight-bold mb-2"><i class="mdi mdi-laptop-mac"></i> Panel Uji Coba Simulasi (Desktop/Laptop)</h5>
          <p class="text-muted small mb-3">Jika tidak menggunakan HP fisik, gunakan kolom di bawah ini untuk mensimulasikan pemindaian kartu NFC secara virtual.</p>
          
          <div class="form-group mb-2">
            <label for="mockNfcSerial">Masukkan UID Kartu NFC Simulasi:</label>
            <div class="input-group">
              <input type="text" class="form-control" id="mockNfcSerial" placeholder="Contoh: 04:33:eb:f2:5a:5e:61" style="font-family: monospace;">
              <button class="btn btn-warning font-weight-bold" type="button" id="btnSubmitMockScan">Simulasi Tap</button>
            </div>
          </div>
          <small class="text-muted d-block small"><em>Petunjuk: Salin salah satu UID kartu mahasiswa yang sudah terdaftar di menu Kelola Mahasiswa untuk menguji status absen sukses.</em></small>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
  .bg-dark-glow {
    background: radial-gradient(circle at center, #2e263d 0%, #17141f 100%);
    border: 1px solid rgba(185, 107, 241, 0.2);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
  }

  .text-gradient-purple {
    background: linear-gradient(135deg, #da8cff 0%, #9a55ff 100%);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
  }

  .radar-container {
    position: relative;
    width: 200px;
    height: 200px;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .radar-ring {
    position: absolute;
    border: 2px solid rgba(185, 107, 241, 0.3);
    border-radius: 50%;
    width: 100%;
    height: 100%;
    box-sizing: border-box;
    opacity: 0;
  }

  .radar-core {
    position: relative;
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, #b96bf1 0%, #7e3eb3 100%);
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    box-shadow: 0 0 25px rgba(185, 107, 241, 0.6);
    z-index: 10;
    transition: all 0.3s ease;
  }

  /* Animasi Radar berdenyut */
  .active-scan .circle-1 {
    animation: radarPulse 3s infinite linear;
  }
  .active-scan .circle-2 {
    animation: radarPulse 3s infinite linear;
    animation-delay: 1s;
  }
  .active-scan .circle-3 {
    animation: radarPulse 3s infinite linear;
    animation-delay: 2s;
  }

  @keyframes radarPulse {
    0% {
      transform: scale(0.5);
      opacity: 1;
    }
    100% {
      transform: scale(1.1);
      opacity: 0;
      border-color: rgba(185, 107, 241, 0);
    }
  }

  .attendance-success-box {
    box-shadow: 0 8px 20px rgba(76, 175, 80, 0.3);
    border-radius: 15px;
    animation: fadeInUp 0.4s ease;
  }

  .attendance-warning-box {
    box-shadow: 0 8px 20px rgba(255, 193, 7, 0.3);
    border-radius: 15px;
    animation: fadeInUp 0.4s ease;
  }

  .attendance-error-box {
    box-shadow: 0 8px 20px rgba(244, 67, 54, 0.3);
    border-radius: 15px;
    animation: fadeInUp 0.4s ease;
  }

  @keyframes fadeInUp {
    from {
      transform: translateY(20px);
      opacity: 0;
    }
    to {
      transform: translateY(0);
      opacity: 1;
    }
  }
</style>
@endpush

@push('scripts')
<script>
  $(document).ready(function() {
    let ndefReader = null;
    let nfcController = null;
    let isActive = false;

    // Aktifkan sensor NFC fisik
    $('#btnActivateScanner').on('click', function() {
      if (isActive) {
        stopNfcScanner();
      } else {
        startNfcScanner();
      }
    });

    async function startNfcScanner() {
      if (!('NDEFReader' in window)) {
        $('#scannerStatusText').text('Web NFC Tidak Didukung').removeClass('badge-outline-light').addClass('badge-outline-danger');
        $('#scannerSubtext').html('Browser desktop Anda tidak mendukung NFC. Gunakan HP Android atau gunakan **Panel Simulasi** di kanan.');
        return;
      }

      try {
        nfcController = new AbortController();
        ndefReader = new NDEFReader();
        await ndefReader.scan({ signal: nfcController.signal });

        // Set State ke Active
        isActive = true;
        $('#btnActivateScanner').html('<i class="mdi mdi-power-off"></i> Nonaktifkan Sensor NFC').removeClass('btn-gradient-primary').addClass('btn-gradient-danger');
        $('#radarContainer').addClass('active-scan');
        $('#radarCore').css('background', 'linear-gradient(135deg, #07cdae 0%, #007764 100%)');
        
        $('#scannerStatusText').text('SCANNER AKTIF').removeClass('badge-outline-light').addClass('badge-outline-success');
        $('#scannerSubtext').text('Sensor menyala. Dekatkan kartu NFC Anda sekarang.');

        // Listener jika terjadi error pemindaian
        ndefReader.addEventListener("readingerror", () => {
          beepFailure();
          showErrorResult('Gagal Membaca Kartu', 'Format NDEF tidak valid atau koneksi sensor terputus.', 'reading_error');
        });

        // Listener jika kartu berhasil dibaca
        ndefReader.addEventListener("reading", ({ serialNumber }) => {
          sendTapRequest(serialNumber);
        });

      } catch (error) {
        console.error("NFC Activate Error: ", error);
        $('#scannerStatusText').text('Gagal Mengakses NFC').removeClass('badge-outline-light').addClass('badge-outline-danger');
        $('#scannerSubtext').text('Pastikan izin akses NFC diberikan dan fitur NFC di HP Anda telah diaktifkan.');
      }
    }

    function stopNfcScanner() {
      if (nfcController) {
        nfcController.abort();
        nfcController = null;
      }
      
      isActive = false;
      $('#btnActivateScanner').html('<i class="mdi mdi-power"></i> Aktifkan Sensor NFC').removeClass('btn-gradient-danger').addClass('btn-gradient-primary');
      $('#radarContainer').removeClass('active-scan');
      
      $('#scannerStatusText').text('Sensor NFC Belum Aktif').removeClass('badge-outline-success badge-outline-danger').addClass('badge-outline-light');
      $('#scannerSubtext').text('Klik tombol di atas untuk mulai memindai kartu mahasiswa.');
    }

    // Mengirim AJAX request untuk mencatat absensi
    function sendTapRequest(serialNumber) {
      $.ajax({
        url: "{{ route('nfc.tap') }}",
        method: "POST",
        headers: {
          'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        data: {
          nfc_serial: serialNumber
        },
        success: function(response) {
          if (response.status === 'success') {
            hapticSuccessFeedback();
            beepSuccess();
            showSuccessResult(
              'Absensi Berhasil!', 
              response.student.name, 
              response.student.nim, 
              response.student.class, 
              response.student.tapped_at
            );
          } else if (response.status === 'warning') {
            hapticWarningFeedback();
            beepWarning();
            showWarningResult(
              'Sudah Absen', 
              response.student.name, 
              response.student.nim, 
              response.student.class,
              response.message
            );
          }
        },
        error: function(xhr) {
          hapticFailureFeedback();
          beepFailure();
          const response = xhr.responseJSON;
          const msg = response ? response.message : 'Terjadi kesalahan sistem.';
          showErrorResult('Absensi Gagal', msg, serialNumber);
        }
      });
    }

    // Tampilkan panel sukses absen
    function showSuccessResult(statusText, name, nim, classLabel, timeStr) {
      const box = `
        <div class="card bg-gradient-success border-0 text-white p-3 text-start attendance-success-box">
          <div class="d-flex align-items-center mb-2">
            <i class="mdi mdi-checkbox-marked-circle-outline me-2 fs-4"></i>
            <strong class="fs-5">${statusText}</strong>
          </div>
          <div class="ps-1">
            <h4 class="font-weight-bold mb-1">${name}</h4>
            <p class="mb-1 small">NIM: ${nim}</p>
            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top border-white-50">
              <span class="badge bg-white text-success font-weight-bold px-2 py-1">${classLabel}</span>
              <span class="small font-weight-bold"><i class="mdi mdi-clock-outline"></i> ${timeStr}</span>
            </div>
          </div>
        </div>
      `;

      $('#attendanceResultCard').html(box).removeClass('d-none');
    }

    // Tampilkan panel warning (Double Tap)
    function showWarningResult(statusText, name, nim, classLabel, message) {
      const box = `
        <div class="card bg-gradient-warning border-0 text-dark p-3 text-start attendance-warning-box">
          <div class="d-flex align-items-center mb-2">
            <i class="mdi mdi-alert-circle-outline me-2 fs-4"></i>
            <strong class="fs-5">${statusText}</strong>
          </div>
          <div class="ps-1">
            <h4 class="font-weight-bold mb-1 text-dark">${name}</h4>
            <p class="mb-1 text-dark-50 small">NIM: ${nim} | Kelas: ${classLabel}</p>
            <p class="mb-0 mt-2 small font-weight-bold"><i class="mdi mdi-information-outline"></i> ${message}</p>
          </div>
        </div>
      `;

      $('#attendanceResultCard').html(box).removeClass('d-none');
    }

    // Tampilkan panel error (Kartu tidak dikenali / Belum didaftarkan)
    function showErrorResult(statusText, message, serial) {
      const box = `
        <div class="card bg-gradient-danger border-0 text-white p-3 text-start attendance-error-box">
          <div class="d-flex align-items-center mb-2">
            <i class="mdi mdi-close-circle-outline me-2 fs-4"></i>
            <strong class="fs-5">${statusText}</strong>
          </div>
          <div class="ps-1">
            <h5 class="mb-1">${message}</h5>
            <p class="mb-0 small text-white-50" style="font-family: monospace; word-break: break-all;">UID: ${serial}</p>
          </div>
        </div>
      `;

      $('#attendanceResultCard').html(box).removeClass('d-none');
    }

    // Handler Uji Coba Simulasi Desktop
    $('#btnSubmitMockScan').on('click', function() {
      const mockUid = $('#mockNfcSerial').val().trim();
      if (!mockUid) {
        alert('Masukkan serial number kartu simulasi terlebih dahulu!');
        return;
      }
      sendTapRequest(mockUid);
    });

    // Premium Haptic & Audio Feedback menggunakan Web Audio API
    function hapticSuccessFeedback() {
      if ('vibrate' in navigator) {
        navigator.vibrate([100]); // Getar 100ms
      }
    }

    function hapticWarningFeedback() {
      if ('vibrate' in navigator) {
        navigator.vibrate([100, 50, 100]); // Getar ganda
      }
    }

    function hapticFailureFeedback() {
      if ('vibrate' in navigator) {
        navigator.vibrate([300]); // Getar lama 300ms
      }
    }

    // Beep Nada Sukses (Frekuensi Tinggi / Riang)
    function beepSuccess() {
      playTone(1200, 0.15);
    }

    // Beep Nada Warning (Dua Beep Menengah)
    function beepWarning() {
      playTone(800, 0.08);
      setTimeout(() => {
        playTone(800, 0.08);
      }, 120);
    }

    // Beep Nada Gagal (Frekuensi Rendah / Buzz)
    function beepFailure() {
      playTone(300, 0.35);
    }

    function playTone(frequency, duration) {
      try {
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioCtx.createOscillator();
        const gainNode = audioCtx.createGain();

        oscillator.connect(gainNode);
        gainNode.connect(audioCtx.destination);

        oscillator.type = 'sine';
        oscillator.frequency.setValueAtTime(frequency, audioCtx.currentTime);
        gainNode.gain.setValueAtTime(0.1, audioCtx.currentTime);

        oscillator.start();
        oscillator.stop(audioCtx.currentTime + duration);
      } catch (e) {
        console.log("Audio feedback error: " + e.message);
      }
    }
  });
</script>
@endpush
