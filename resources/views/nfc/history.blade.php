@extends('layouts.master')

@section('content')
<div class="page-header">
  <h3 class="page-title">
    <span class="page-title-icon bg-gradient-primary text-white me-2">
      <i class="mdi mdi-history"></i>
    </span> Riwayat Kehadiran NFC
  </h3>
  <nav aria-label="breadcrumb">
    <div class="d-flex justify-content-between">
      <a href="{{ route('nfc.scan.index') }}" class="btn btn-gradient-success btn-fw me-2"><i class="mdi mdi-cellphone-nfc"></i> Buka Scanner</a>
      <a href="{{ route('nfc.students.index') }}" class="btn btn-gradient-primary btn-fw"><i class="mdi mdi-account-multiple"></i> Kelola Mahasiswa</a>
    </div>
  </nav>
</div>

<!-- Baris Statistik Dashboard -->
<div class="row">
  <div class="col-md-4 stretch-card grid-margin">
    <div class="card bg-gradient-success card-img-holder text-white">
      <div class="card-body">
        <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
        <h4 class="font-weight-normal mb-3">Kehadiran Hari Ini <i class="mdi mdi-checkbox-marked-circle-outline mdi-24px float-right"></i>
        </h4>
        <h2 class="mb-3">{{ $stats['today_attendance'] }}</h2>
        <h6 class="card-text">Total tap kehadiran pada hari ini</h6>
      </div>
    </div>
  </div>

  <div class="col-md-4 stretch-card grid-margin">
    <div class="card bg-gradient-info card-img-holder text-white">
      <div class="card-body">
        <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
        <h4 class="font-weight-normal mb-3">Total Mahasiswa <i class="mdi mdi-account-multiple mdi-24px float-right"></i>
        </h4>
        <h2 class="mb-3">{{ $stats['total_students'] }}</h2>
        <h6 class="card-text">Jumlah mahasiswa terdaftar di sistem</h6>
      </div>
    </div>
  </div>

  <div class="col-md-4 stretch-card grid-margin">
    <div class="card bg-gradient-primary card-img-holder text-white">
      <div class="card-body">
        <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
        <h4 class="font-weight-normal mb-3">Kartu NFC Aktif <i class="mdi mdi-nfc mdi-24px float-right"></i>
        </h4>
        <h2 class="mb-3">{{ $stats['registered_cards'] }}</h2>
        <h6 class="card-text">Mahasiswa yang telah memiliki kartu NFC</h6>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Log Absensi Kehadiran</h4>
        <p class="card-description"> Menampilkan semua riwayat tap kartu mahasiswa real-time </p>
        
        <div class="table-responsive">
          <table class="table table-striped table-hover" id="historyTable">
            <thead>
              <tr>
                <th>Waktu Tap</th>
                <th>Tanggal</th>
                <th>NIM</th>
                <th>Nama Lengkap</th>
                <th>Kelas</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @forelse($history as $item)
              <tr>
                <td>
                  <strong class="text-primary fs-6">
                    <i class="mdi mdi-clock-outline"></i> {{ \Carbon\Carbon::parse($item->tapped_at)->format('H:i:s') }} WIB
                  </strong>
                </td>
                <td>{{ \Carbon\Carbon::parse($item->tapped_at)->translatedFormat('d F Y') }}</td>
                <td><strong>{{ $item->nim }}</strong></td>
                <td>{{ $item->name }}</td>
                <td><label class="badge badge-outline-secondary font-weight-bold">{{ $item->class }}</label></td>
                <td>
                  @if($item->status == 'Hadir')
                    <label class="badge badge-gradient-success"><i class="mdi mdi-check"></i> {{ $item->status }}</label>
                  @else
                    <label class="badge badge-gradient-warning">{{ $item->status }}</label>
                  @endif
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="6" class="text-center text-muted">Belum ada riwayat tap absensi masuk hari ini.</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  $(document).ready(function() {
    // Inisialisasi DataTable untuk riwayat kehadiran
    if ($('#historyTable tbody tr').length > 1 || ($('#historyTable tbody tr').length === 1 && !$('#historyTable tbody tr td').hasClass('text-center'))) {
      $('#historyTable').DataTable({
        "order": [[0, "desc"]], // Urutkan berdasarkan waktu tap terbaru
        "language": {
          "search": "Cari Log:",
          "lengthMenu": "Tampilkan _MENU_ log",
          "zeroRecords": "Tidak ditemukan riwayat absensi yang cocok",
          "info": "Menampilkan _START_ hingga _END_ dari _TOTAL_ kehadiran",
          "infoEmpty": "Menampilkan 0 hingga 0 dari 0 kehadiran",
          "paginate": {
            "first": "Pertama",
            "last": "Terakhir",
            "next": "Berikutnya",
            "previous": "Sebelumnya"
          }
        }
      });
    }
  });
</script>
@endpush
