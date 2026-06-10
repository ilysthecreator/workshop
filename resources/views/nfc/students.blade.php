@extends('layouts.master')

@section('content')
<div class="page-header">
  <h3 class="page-title">
    <span class="page-title-icon bg-gradient-primary text-white me-2">
      <i class="mdi mdi-account-multiple"></i>
    </span> Kelola Mahasiswa NFC
  </h3>
  <nav aria-label="breadcrumb">
    <ul class="breadcrumb">
      <li class="breadcrumb-item active" aria-current="page">
        <span></span>Data Mahasiswa & Kartu NFC <i class="mdi mdi-alert-circle-outline icon-sm text-primary align-middle"></i>
      </li>
    </ul>
  </nav>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
  <strong>Sukses!</strong> {{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
  <strong>Error!</strong> {{ session('error') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row">
  <!-- Form Tambah Mahasiswa -->
  <div class="col-md-4 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Tambah Mahasiswa Baru</h4>
        <p class="card-description"> Masukkan detail mahasiswa untuk didaftarkan ke sistem </p>
        
        <form class="forms-sample" action="{{ route('nfc.students.store') }}" method="POST">
          @csrf
          <div class="form-group">
            <label for="nim">NIM (Nomor Induk Mahasiswa)</label>
            <input type="text" class="form-control @error('nim') is-invalid @enderror" id="nim" name="nim" placeholder="Contoh: 220101001" required value="{{ old('nim') }}">
            @error('nim')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="form-group">
            <label for="name">Nama Lengkap</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Contoh: Ahmad Fauzi" required value="{{ old('name') }}">
            @error('name')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="form-group">
            <label for="class">Kelas / Program Studi</label>
            <input type="text" class="form-control @error('class') is-invalid @enderror" id="class" name="class" placeholder="Contoh: TI-4A" required value="{{ old('class') }}">
            @error('class')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <button type="submit" class="btn btn-gradient-primary me-2 btn-fw">Simpan Data</button>
        </form>
      </div>
    </div>
  </div>

  <!-- Tabel Mahasiswa -->
  <div class="col-md-8 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Daftar Mahasiswa</h4>
        <p class="card-description"> Kelola kartu NFC mahasiswa </p>
        
        <div class="table-responsive">
          <table class="table table-hover" id="studentsTable">
            <thead>
              <tr>
                <th>NIM</th>
                <th>Nama</th>
                <th>Kelas</th>
                <th>UID Kartu NFC</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($students as $student)
              <tr>
                <td><strong>{{ $student->nim }}</strong></td>
                <td>{{ $student->name }}</td>
                <td><label class="badge badge-light">{{ $student->class }}</label></td>
                <td>
                  @if($student->nfc_serial)
                    <span class="badge badge-gradient-success">
                      <i class="mdi mdi-nfc"></i> {{ $student->nfc_serial }}
                    </span>
                  @else
                    <span class="badge badge-gradient-danger">
                      <i class="mdi mdi-nfc-variant"></i> Belum Terdaftar
                    </span>
                  @endif
                </td>
                <td>
                  <div class="d-flex align-items-center">
                    <button class="btn btn-sm btn-gradient-info me-2 btn-register-nfc" 
                            data-id="{{ $student->id }}" 
                            data-name="{{ $student->name }}"
                            data-nim="{{ $student->nim }}">
                      <i class="mdi mdi-nfc-tap"></i> Register NFC
                    </button>
                    
                    <form action="{{ route('nfc.students.destroy', $student->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mahasiswa ini? Semua riwayat kehadiran mahasiswa ini juga akan terhapus.');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-gradient-danger">
                        <i class="mdi mdi-delete"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="5" class="text-center text-muted">Belum ada data mahasiswa. Silakan tambahkan melalui form di samping.</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Registrasi Kartu NFC -->
<div class="modal fade" id="registerNfcModal" tabindex="-1" aria-labelledby="registerNfcModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center">
      <div class="modal-header">
        <h5 class="modal-title" id="registerNfcModalLabel">Daftarkan Kartu NFC</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="btnCloseModal"></button>
      </div>
      <div class="modal-body p-5">
        <div id="modalTargetInfo" class="mb-4">
          <h4 id="targetStudentName" class="text-primary font-weight-bold"></h4>
          <p id="targetStudentNim" class="text-muted"></p>
        </div>

        <!-- Animasi Pemindaian -->
        <div class="nfc-scan-container mb-4">
          <div class="nfc-radar">
            <i class="mdi mdi-nfc-tap text-primary" style="font-size: 80px;"></i>
            <div class="nfc-pulse pulse-1"></div>
            <div class="nfc-pulse pulse-2"></div>
          </div>
        </div>

        <h4 id="nfcStatusText" class="text-dark font-weight-bold">Siap Memindai...</h4>
        <p id="nfcSubtext" class="text-muted small">Dekatkan kartu NFC ke bagian belakang smartphone Anda untuk membaca serial number.</p>

        <!-- Form Tersembunyi -->
        <div id="nfcResultForm" class="d-none mt-4">
          <div class="alert alert-success">
            <i class="mdi mdi-check-circle"></i> Kartu Berhasil Terdeteksi!
          </div>
          <div class="form-group">
            <label class="font-weight-bold">UID Serial Number:</label>
            <input type="text" class="form-control text-center font-weight-bold text-success fs-5" id="detectedSerial" readonly style="letter-spacing: 2px;">
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary btn-fw" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-gradient-primary btn-fw d-none" id="btnSaveNfc">Simpan Kartu</button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('styles')
<style>
  .nfc-scan-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 150px;
    position: relative;
  }
  
  .nfc-radar {
    position: relative;
    width: 120px;
    height: 120px;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 50%;
    background: rgba(185, 107, 241, 0.05);
    z-index: 10;
  }

  .nfc-pulse {
    position: absolute;
    border: 2px solid #b96bf1;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    opacity: 0;
    z-index: 1;
  }

  .pulse-1 {
    animation: nfcPulseAnim 2s infinite linear;
  }

  .pulse-2 {
    animation: nfcPulseAnim 2s infinite linear;
    animation-delay: 1s;
  }

  @keyframes nfcPulseAnim {
    0% {
      transform: scale(0.8);
      opacity: 0.8;
    }
    100% {
      transform: scale(2.2);
      opacity: 0;
    }
  }

  #detectedSerial {
    background: #f3f3f3;
    border: 2px solid #57c7d4;
  }
</style>
@endpush

@push('scripts')
<script>
  $(document).ready(function() {
    // Inisialisasi DataTable jika datanya ada
    if ($('#studentsTable tbody tr').length > 1 || ($('#studentsTable tbody tr').length === 1 && !$('#studentsTable tbody tr td').hasClass('text-center'))) {
      $('#studentsTable').DataTable({
        "language": {
          "search": "Cari Mahasiswa:",
          "lengthMenu": "Tampilkan _MENU_ data",
          "zeroRecords": "Tidak ditemukan data yang cocok",
          "info": "Menampilkan _START_ hingga _END_ dari _TOTAL_ mahasiswa",
          "infoEmpty": "Menampilkan 0 hingga 0 dari 0 mahasiswa",
          "paginate": {
            "first": "Pertama",
            "last": "Terakhir",
            "next": "Berikutnya",
            "previous": "Sebelumnya"
          }
        }
      });
    }

    let activeStudentId = null;
    let ndefReader = null;
    let nfcController = null;

    // Aksi tombol Register NFC
    $('.btn-register-nfc').on('click', function() {
      activeStudentId = $(this).data('id');
      const studentName = $(this).data('name');
      const studentNim = $(this).data('nim');

      $('#targetStudentName').text(studentName);
      $('#targetStudentNim').text('NIM: ' + studentNim);
      
      // Reset Modal State
      $('#nfcStatusText').text('Siap Memindai...').removeClass('text-danger text-success').addClass('text-dark');
      $('#nfcSubtext').text('Dekatkan kartu NFC ke bagian belakang smartphone Anda untuk membaca serial number.');
      $('#nfcResultForm').addClass('d-none');
      $('#detectedSerial').val('');
      $('#btnSaveNfc').addClass('d-none');
      $('.nfc-pulse').removeClass('d-none');

      // Tampilkan Modal
      const registerModal = new bootstrap.Modal(document.getElementById('registerNfcModal'));
      registerModal.show();

      // Mulai Sensor Web NFC
      startNfcScanner();
    });

    // Sensor Web NFC logic
    async function startNfcScanner() {
      if (!('NDEFReader' in window)) {
        $('#nfcStatusText').text('Web NFC Tidak Didukung!').addClass('text-danger');
        $('#nfcSubtext').html('Browser Anda tidak mendukung Web NFC. Silakan gunakan <strong>Android Google Chrome</strong> via <strong>HTTPS/ngrok</strong>.');
        
        // Buat Tombol Simulasi Input jika di Desktop
        showMockInput();
        return;
      }

      try {
        nfcController = new AbortController();
        ndefReader = new NDEFReader();
        await ndefReader.scan({ signal: nfcController.signal });

        $('#nfcStatusText').text('Dekatkan Kartu NFC...').addClass('text-primary');

        ndefReader.addEventListener("readingerror", () => {
          $('#nfcStatusText').text('Gagal Membaca Kartu!').addClass('text-danger');
          $('#nfcSubtext').text('Format kartu tidak didukung atau terjadi kesalahan koneksi sensor.');
        });

        ndefReader.addEventListener("reading", ({ serialNumber }) => {
          // Sukses membaca UID
          hapticSuccessFeedback();
          beepSuccess();

          $('#detectedSerial').val(serialNumber);
          $('#nfcStatusText').text('Membaca Sukses!').removeClass('text-primary text-danger').addClass('text-success');
          $('#nfcSubtext').text('Kartu terdeteksi dengan sukses. Klik Simpan untuk mendaftarkan.');
          
          $('#nfcResultForm').removeClass('d-none');
          $('#btnSaveNfc').removeClass('d-none');
          $('.nfc-pulse').addClass('d-none');

          // Hentikan scan setelah terbaca
          stopNfcScanner();
        });

      } catch (error) {
        console.error("NFC Scan Error: ", error);
        $('#nfcStatusText').text('Izin Sensor NFC Ditolak').addClass('text-danger');
        $('#nfcSubtext').text('Pastikan Anda memberikan izin akses NFC untuk web ini di smartphone Anda.');
        showMockInput();
      }
    }

    // Fungsi untuk menghentikan scanning
    function stopNfcScanner() {
      if (nfcController) {
        nfcController.abort();
        nfcController = null;
      }
    }

    // Tampilkan mock input apabila di Desktop/Laptop
    function showMockInput() {
      // Jika tombol simulasi sudah ada, jangan buat lagi
      if ($('#mockInputContainer').length > 0) return;

      const mockHtml = `
        <div id="mockInputContainer" class="mt-4 pt-3 border-top">
          <div class="badge badge-gradient-warning mb-2 text-dark font-weight-bold">Mode Simulasi (Desktop/Non-NFC)</div>
          <div class="input-group">
            <input type="text" class="form-control text-center" id="mockNfcSerial" placeholder="Masukkan UID acak (e.g. 04:a3:7b:f8:29)">
            <button class="btn btn-primary" type="button" id="btnMockScan">Simulasi Tap</button>
          </div>
          <small class="text-muted mt-1 d-block">Gunakan kolom ini untuk menguji pendaftaran tanpa sensor NFC fisik.</small>
        </div>
      `;

      $('.modal-body').append(mockHtml);

      // Event handler untuk mock scan
      $('#btnMockScan').off('click').on('click', function() {
        const mockUid = $('#mockNfcSerial').val().trim();
        if (!mockUid) {
          alert('Masukkan serial number simulasi terlebih dahulu!');
          return;
        }

        beepSuccess();
        $('#detectedSerial').val(mockUid);
        $('#nfcStatusText').text('Membaca Sukses! (Simulasi)').removeClass('text-primary text-danger').addClass('text-success');
        $('#nfcResultForm').removeClass('d-none');
        $('#btnSaveNfc').removeClass('d-none');
        $('.nfc-pulse').addClass('d-none');
      });
    }

    // Klik Simpan Data NFC ke Database
    $('#btnSaveNfc').on('click', function() {
      const serialNumber = $('#detectedSerial').val();

      if (!activeStudentId || !serialNumber) {
        alert('Data mahasiswa atau UID kartu kosong!');
        return;
      }

      $.ajax({
        url: "{{ route('nfc.register-card') }}",
        method: "POST",
        headers: {
          'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        data: {
          student_id: activeStudentId,
          nfc_serial: serialNumber
        },
        success: function(response) {
          stopNfcScanner();
          $('#registerNfcModal').modal('hide');
          alert(response.message);
          window.location.reload();
        },
        error: function(xhr) {
          const errMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan sistem saat mendaftarkan kartu.';
          alert(errMsg);
        }
      });
    });

    // Ketika modal ditutup, hentikan scanner
    $('#registerNfcModal').on('hidden.bs.modal', function () {
      stopNfcScanner();
      $('#mockInputContainer').remove();
    });

    // Premium Haptic & Audio Feedback menggunakan Web Audio API
    function hapticSuccessFeedback() {
      if ('vibrate' in navigator) {
        navigator.vibrate([100]); // Getar 100ms
      }
    }

    function beepSuccess() {
      try {
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioCtx.createOscillator();
        const gainNode = audioCtx.createGain();

        oscillator.connect(gainNode);
        gainNode.connect(audioCtx.destination);

        oscillator.type = 'sine';
        oscillator.frequency.setValueAtTime(1200, audioCtx.currentTime); // Beep nyaring frekuensi tinggi
        gainNode.gain.setValueAtTime(0.1, audioCtx.currentTime);

        oscillator.start();
        oscillator.stop(audioCtx.currentTime + 0.15); // bunyi selama 150ms
      } catch (e) {
        console.log("Web Audio API not supported on this browser: " + e.message);
      }
    }
  });
</script>
@endpush
