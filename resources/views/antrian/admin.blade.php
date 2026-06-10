@extends('layouts.master')

@push('styles')
<style>
    .nomor-sekarang {
        font-size: 80px;
        font-weight: 800;
        color: #b66dff;
        line-height: 1;
        margin: 15px 0;
        text-shadow: 0 4px 10px rgba(182, 109, 255, 0.15);
    }
    .sse-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #1bcfb4;
        margin-right: 5px;
        animation: pulse 1.5s infinite;
    }
    @keyframes pulse {
        0% { opacity: 0.4; }
        50% { opacity: 1; }
        100% { opacity: 0.4; }
    }
    .table-clickable tbody tr {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }
    .table-clickable tbody tr:hover {
        background-color: rgba(182, 109, 255, 0.05);
    }
    .card-stat-custom {
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .list-title {
        font-size: 15px;
        font-weight: 600;
        border-bottom: 2px solid #f3f3f3;
        padding-bottom: 8px;
    }
</style>
@endpush

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
            <i class="mdi mdi-broadcast"></i>
        </span> Kelola Antrian Real-Time (SSE)
    </h3>
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item">
                <span class="badge bg-light text-dark p-2 border" id="sseBadge">
                    <span class="sse-dot" id="sseDot"></span> SSE Active
                </span>
            </li>
        </ul>
    </nav>
</div>

{{-- Statistik Kinerja --}}
<div class="row mb-4">
    <div class="col-md-3 grid-margin stretch-card">
        <div class="card bg-gradient-warning card-img-holder text-white card-stat-custom">
            <div class="card-body py-3 px-4">
                <h6 class="font-weight-normal mb-2">Menunggu <i class="mdi mdi-clock-outline float-end mdi-24px"></i></h6>
                <h2 class="mb-0" id="countMenunggu">0</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 grid-margin stretch-card">
        <div class="card bg-gradient-success card-img-holder text-white card-stat-custom">
            <div class="card-body py-3 px-4">
                <h6 class="font-weight-normal mb-2">Sedang Dipanggil <i class="mdi mdi-volume-high float-end mdi-24px"></i></h6>
                <h2 class="mb-0" id="countDipanggil">0</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 grid-margin stretch-card">
        <div class="card bg-gradient-danger card-img-holder text-white card-stat-custom">
            <div class="card-body py-3 px-4">
                <h6 class="font-weight-normal mb-2">Terlambat <i class="mdi mdi-alert-circle-outline float-end mdi-24px"></i></h6>
                <h2 class="mb-0" id="countTerlambat">0</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 grid-margin stretch-card">
        <div class="card bg-gradient-info card-img-holder text-white card-stat-custom">
            <div class="card-body py-3 px-4">
                <h6 class="font-weight-normal mb-2">Selesai <i class="mdi mdi-check-circle-outline float-end mdi-24px"></i></h6>
                <h2 class="mb-0" id="countSelesai">0</h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Panel Panggilan & List Terlambat --}}
    <div class="col-lg-5 col-md-12 grid-margin stretch-card d-flex flex-column">
        {{-- Panel Panggilan Utama --}}
        <div class="card mb-4">
            <div class="card-body text-center py-4">
                <h4 class="card-title text-start mb-3"><i class="mdi mdi-volume-high text-primary"></i> Panggilan Sekarang</h4>
                <hr class="mt-0">
                <div id="currentDisplay">
                    @if($sedangDipanggil)
                        <p class="text-muted mb-1 small text-uppercase" style="letter-spacing: 1px;">Nomor Antrian</p>
                        <div class="nomor-sekarang">{{ $sedangDipanggil->nomor_antrian }}</div>
                        <h4 class="fw-bold mb-3">{{ $sedangDipanggil->nama }}</h4>
                    @else
                        <div class="py-4">
                            <i class="mdi mdi-account-off text-muted mb-2 mdi-36px d-block"></i>
                            <p class="text-muted mb-0">Belum ada antrian yang sedang dipanggil.</p>
                        </div>
                    @endif
                </div>
                <hr>
                <div class="row g-2 px-2">
                    <div class="col-12">
                        <button class="btn btn-gradient-success w-100 btn-lg font-weight-bold" id="btnPanggil" onclick="panggilBerikutnya()">
                            <i class="mdi mdi-play-circle-outline me-1"></i> Panggil Berikutnya
                        </button>
                    </div>
                    <div class="col-6">
                        <button class="btn btn-gradient-warning text-dark w-100 font-weight-semibold" id="btnTerlambat" onclick="tandaiTerlambat()" {{ !$sedangDipanggil ? 'disabled' : '' }}>
                            <i class="mdi mdi-clock-alert me-1"></i> Lewati / Terlambat
                        </button>
                    </div>
                    <div class="col-6">
                        <button class="btn btn-gradient-primary w-100 font-weight-semibold" id="btnSelesai" onclick="selesaikanCurrent()" {{ !$sedangDipanggil ? 'disabled' : '' }}>
                            <i class="mdi mdi-check me-1"></i> Selesaikan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Daftar Terlambat --}}
        <div class="card grow">
            <div class="card-body">
                <h5 class="list-title text-danger mb-3"><i class="mdi mdi-alert-circle"></i> Antrian Terlambat (Late)</h5>
                <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                    <table class="table table-hover table-clickable" id="tableTerlambat">
                        <thead>
                            <tr>
                                <th width="20%">No</th>
                                <th>Nama</th>
                                <th width="25%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="terlambatBody">
                            @forelse($daftarAntrian->where('status', 'terlambat') as $item)
                            <tr data-id="{{ $item->id }}" class="terlambat-row" title="Double click untuk panggil ulang">
                                <td><strong>{{ $item->nomor_antrian }}</strong></td>
                                <td>{{ $item->nama }}</td>
                                <td>
                                    <button class="btn btn-gradient-primary btn-sm btn-icon btn-panggil-action" data-id="{{ $item->id }}" title="Panggil Ulang">
                                        <i class="mdi mdi-volume-high"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">Tidak ada antrian terlambat.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <small class="text-muted d-block mt-2"><i class="mdi mdi-information-outline"></i> Klik tombol <i class="mdi mdi-volume-high text-primary"></i> atau double click pada baris untuk panggil ulang.</small>
            </div>
        </div>
    </div>

    {{-- Daftar Tunggu & Riwayat Selesai --}}
    <div class="col-lg-7 col-md-12 grid-margin stretch-card d-flex flex-column">
        {{-- Daftar Tunggu --}}
        <div class="card mb-4 grow">
            <div class="card-body">
                <h5 class="list-title text-warning mb-3"><i class="mdi mdi-account-multiple"></i> Antrian Menunggu (Waiting)</h5>
                <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                    <table class="table table-hover table-clickable" id="tableMenunggu">
                        <thead>
                            <tr>
                                <th width="20%">No</th>
                                <th>Nama</th>
                                <th width="25%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="menungguBody">
                            @forelse($daftarAntrian->where('status', 'menunggu') as $item)
                            <tr data-id="{{ $item->id }}" class="menunggu-row" title="Double click untuk panggil">
                                <td><strong>{{ $item->nomor_antrian }}</strong></td>
                                <td>{{ $item->nama }}</td>
                                <td>
                                    <button class="btn btn-gradient-success btn-sm btn-icon btn-panggil-action" data-id="{{ $item->id }}" title="Panggil">
                                        <i class="mdi mdi-volume-high"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">Belum ada antrian menunggu.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Riwayat Selesai --}}
        <div class="card grow">
            <div class="card-body">
                <h5 class="list-title text-info mb-3"><i class="mdi mdi-check-circle"></i> Riwayat Selesai Hari Ini</h5>
                <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                    <table class="table table-hover" id="tableSelesai">
                        <thead>
                            <tr>
                                <th width="25%">No</th>
                                <th>Nama</th>
                                <th width="35%">Waktu Selesai</th>
                            </tr>
                        </thead>
                        <tbody id="selesaiBody">
                            @forelse($daftarAntrian->where('status', 'selesai') as $item)
                            <tr>
                                <td><strong>{{ $item->nomor_antrian }}</strong></td>
                                <td>{{ $item->nama }}</td>
                                <td>{{ $item->updated_at->format('H:i') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">Belum ada antrian yang diselesaikan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Toast Notification --}}
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050;">
    <div id="toast" class="toast align-items-center text-white bg-dark border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMsg"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    $(document).ready(function() {
        // Event handlers for double click rows and recall buttons
        $(document).on('dblclick', '.terlambat-row, .menunggu-row', function() {
            const id = $(this).data('id');
            panggilUlang(id);
        });

        $(document).on('click', '.btn-panggil-action', function(e) {
            e.stopPropagation();
            const id = $(this).data('id');
            panggilUlang(id);
        });
    });

    // Toast helper
    function showToast(msg, isError = false) {
        const toastEl = document.getElementById('toast');
        const toastMsg = document.getElementById('toastMsg');
        toastMsg.textContent = msg;
        
        if (isError) {
            toastEl.classList.remove('bg-dark');
            toastEl.classList.add('bg-danger');
        } else {
            toastEl.classList.remove('bg-danger');
            toastEl.classList.add('bg-dark');
        }
        
        const t = new bootstrap.Toast(toastEl);
        t.show();
    }

    // Aksi memanggil berikutnya
    function panggilBerikutnya() {
        const btn = document.getElementById('btnPanggil');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Memproses...';

        fetch('{{ route("antrian.panggil") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = originalText;
            if (data.success) {
                showToast(data.message);
            } else {
                showToast(data.message, true);
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = originalText;
            showToast('Error: ' + err.message, true);
        });
    }

    // Aksi menandai terlambat
    function tandaiTerlambat() {
        const btn = document.getElementById('btnTerlambat');
        btn.disabled = true;

        fetch('{{ route("antrian.terlambat") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            if (data.success) {
                showToast(data.message);
            } else {
                showToast(data.message, true);
            }
        })
        .catch(err => {
            btn.disabled = false;
            showToast('Error: ' + err.message, true);
        });
    }

    // Aksi menyelesaikan panggilan saat ini
    function selesaikanCurrent() {
        const btn = document.getElementById('btnSelesai');
        btn.disabled = true;

        fetch('{{ route("antrian.selesaikan") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            if (data.success) {
                showToast(data.message);
            } else {
                showToast(data.message, true);
            }
        })
        .catch(err => {
            btn.disabled = false;
            showToast('Error: ' + err.message, true);
        });
    }

    // Aksi panggil ulang (recall) antrian tertentu
    function panggilUlang(id) {
        fetch(`/admin/panggil-ulang/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(data.message);
            } else {
                showToast(data.message, true);
            }
        })
        .catch(err => {
            showToast('Error: ' + err.message, true);
        });
    }

    // SSE Connection
    const eventSource = new EventSource('{{ route("sse.antrian") }}');

    eventSource.onmessage = function(event) {
        const data = JSON.parse(event.data);
        updateDashboard(data);
    };

    eventSource.onerror = function() {
        const badge = document.getElementById('sseBadge');
        const dot = document.getElementById('sseDot');
        badge.className = 'badge bg-light text-danger p-2 border';
        dot.style.background = '#fe7c96';
    };

    eventSource.onopen = function() {
        const badge = document.getElementById('sseBadge');
        const dot = document.getElementById('sseDot');
        badge.className = 'badge bg-light text-dark p-2 border';
        dot.style.background = '#1bcfb4';
    };

    // Sinkronisasi data real-time ke UI dashboard
    function updateDashboard(data) {
        const daftar = data.daftar;
        const dipanggil = data.dipanggil;

        // 1. Update counter statistik
        const waitingCount = daftar.filter(d => d.status === 'menunggu').length;
        const callingCount = daftar.filter(d => d.status === 'dipanggil').length;
        const lateCount = daftar.filter(d => d.status === 'terlambat').length;
        const selesaiCount = daftar.filter(d => d.status === 'selesai').length;

        document.getElementById('countMenunggu').textContent = waitingCount;
        document.getElementById('countDipanggil').textContent = callingCount;
        document.getElementById('countTerlambat').textContent = lateCount;
        document.getElementById('countSelesai').textContent = selesaiCount;

        // 2. Update panel nomor sekarang
        const display = document.getElementById('currentDisplay');
        const btnTerlambat = document.getElementById('btnTerlambat');
        const btnSelesai = document.getElementById('btnSelesai');

        if (dipanggil) {
            display.innerHTML = `
                <p class="text-muted mb-1 small text-uppercase" style="letter-spacing: 1px;">Nomor Antrian</p>
                <div class="nomor-sekarang">${dipanggil.nomor_antrian}</div>
                <h4 class="fw-bold mb-3">${dipanggil.nama}</h4>
            `;
            btnTerlambat.disabled = false;
            btnSelesai.disabled = false;
        } else {
            display.innerHTML = `
                <div class="py-4">
                    <i class="mdi mdi-account-off text-muted mb-2 mdi-36px d-block"></i>
                    <p class="text-muted mb-0">Belum ada antrian yang sedang dipanggil.</p>
                </div>
            `;
            btnTerlambat.disabled = true;
            btnSelesai.disabled = true;
        }

        // 3. Update Tabel Menunggu
        const menungguBody = document.getElementById('menungguBody');
        const daftarMenunggu = daftar.filter(d => d.status === 'menunggu');
        if (daftarMenunggu.length === 0) {
            menungguBody.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-3">Belum ada antrian menunggu.</td></tr>';
        } else {
            let html = '';
            daftarMenunggu.forEach(item => {
                html += `
                    <tr data-id="${item.id}" class="menunggu-row" title="Double click untuk panggil">
                        <td><strong>${item.nomor_antrian}</strong></td>
                        <td>${item.nama}</td>
                        <td>
                            <button class="btn btn-gradient-success btn-sm btn-icon btn-panggil-action" data-id="${item.id}" title="Panggil">
                                <i class="mdi mdi-volume-high"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            menungguBody.innerHTML = html;
        }

        // 4. Update Tabel Terlambat
        const terlambatBody = document.getElementById('terlambatBody');
        const daftarTerlambat = daftar.filter(d => d.status === 'terlambat');
        if (daftarTerlambat.length === 0) {
            terlambatBody.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-3">Tidak ada antrian terlambat.</td></tr>';
        } else {
            let html = '';
            daftarTerlambat.forEach(item => {
                html += `
                    <tr data-id="${item.id}" class="terlambat-row" title="Double click untuk panggil ulang">
                        <td><strong>${item.nomor_antrian}</strong></td>
                        <td>${item.nama}</td>
                        <td>
                            <button class="btn btn-gradient-primary btn-sm btn-icon btn-panggil-action" data-id="${item.id}" title="Panggil Ulang">
                                <i class="mdi mdi-volume-high"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            terlambatBody.innerHTML = html;
        }

        // 5. Update Tabel Selesai
        const selesaiBody = document.getElementById('selesaiBody');
        const daftarSelesai = daftar.filter(d => d.status === 'selesai');
        if (daftarSelesai.length === 0) {
            selesaiBody.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-3">Belum ada antrian yang diselesaikan.</td></tr>';
        } else {
            let html = '';
            // Ambil waktu dari updated_at (kita gunakan parser javascript sederhana)
            daftarSelesai.forEach(item => {
                html += `
                    <tr>
                        <td><strong>${item.nomor_antrian}</strong></td>
                        <td>${item.nama}</td>
                        <td>${new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}</td>
                    </tr>
                `;
            });
            selesaiBody.innerHTML = html;
        }
    }
</script>
@endpush
