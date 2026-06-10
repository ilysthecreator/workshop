@extends('layouts.master')

@section('content')
<div class="page-header">
  <h3 class="page-title">
    <span class="page-title-icon bg-gradient-primary text-white me-2">
      <i class="mdi mdi-tag-multiple"></i>
    </span> Master Barang
  </h3>
  <nav aria-label="breadcrumb">
    <button type="button" class="btn btn-gradient-primary btn-fw mb-2" data-bs-toggle="modal" data-bs-toggle="modal" data-bs-target="#createModal">
      + Tambah Barang
    </button>
  </nav>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
@if ($errors->any())
    <div class="alert alert-danger mt-3">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row">
  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Data Barang</h4>
        <div class="table-responsive">
          <div class="row mb-3 align-items-end">
              <div class="col-md-3">
                  <label class="form-label form-label-sm">Kolom Mulai (X) [1-5]</label>
                  <input type="number" id="inputStartX" class="form-control form-control-sm" min="1" max="5" value="1">
              </div>
              <div class="col-md-3">
                  <label class="form-label form-label-sm">Baris Mulai (Y) [1-8]</label>
                  <input type="number" id="inputStartY" class="form-control form-control-sm" min="1" max="8" value="1">
              </div>
              <div class="col-md-6">
                  <button type="button" id="btnCetakPdf" class="btn btn-gradient-warning btn-icon-text w-100">
                      <i class="mdi mdi-printer btn-icon-prepend"></i> Cetak PDF Terpilih
                  </button>
              </div>
              <div class="col-12 mt-1">
                  <small class="text-muted d-block">Pilih data untuk dicetak dengan format kertas label Tom & Jerry 108 (38x18mm)</small>
              </div>
          </div>
          
          <form id="printPdfForm" action="{{ route('barang.printPdf') }}" method="POST" target="_blank" style="display: none;">
              @csrf
              <input type="hidden" name="start_x" id="hiddenStartX" value="1">
              <input type="hidden" name="start_y" id="hiddenStartY" value="1">
          </form>
          
          <table class="table table-hover table-sm align-middle border-bottom" id="barangTable">
            <thead>
              <tr>
                <th style="width: 50px; text-align: center;">
                  <input type="checkbox" id="selectAll">
                </th>
                <th>ID Barang</th>
                <th>Nama Barang</th>
                <th>Harga</th>
                <th>Waktu Input</th>
                <th width="15%">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach($barangs as $b)
              <tr>
                <td style="text-align: center;">
                  <input type="checkbox" name="selected_items[]" value="{{ $b->id_barang }}" class="selectItem">
                </td>
                <td><strong>{{ $b->id_barang }}</strong></td>
                <td>{{ $b->nama }}</td>
                <td>Rp {{ number_format($b->harga, 0, ',', '.') }}</td>
                <td>{{ $b->timestamp }}</td>
                <td>
                  <div class="d-flex">
                      <button type="button" class="btn btn-sm btn-gradient-warning me-2 editBtn" 
                          data-bs-toggle="modal" 
                          data-bs-target="#editModal" 
                          data-id="{{ $b->id_barang }}"
                          data-nama="{{ $b->nama }}"
                          data-harga="{{ $b->harga }}">
                          Edit
                      </button>
                      <form action="{{ route('barang.destroy', $b->id_barang) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-gradient-danger">Hapus</button>
                      </form>
                  </div>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Create -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createModalLabel">Tambah Barang Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('barang.store') }}" method="POST">
        @csrf
        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">Nama Barang</label>
                <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required maxlength="50" placeholder="Contoh: Pensil 2B">
            </div>
            <div class="mb-3">
                <label class="form-label">Harga (Rp)</label>
                <input type="number" name="harga" class="form-control" value="{{ old('harga') }}" required placeholder="Contoh: 5000">
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          <button type="submit" class="btn btn-gradient-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Edit Barang</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">ID Barang</label>
                <input type="text" id="edit_id" class="form-control" disabled>
                <small class="text-muted">ID Barang tidak dapat diubah</small>
            </div>
            <div class="mb-3">
                <label class="form-label">Nama Barang</label>
                <input type="text" name="nama" id="edit_nama" class="form-control" required maxlength="50">
            </div>
            <div class="mb-3">
                <label class="form-label">Harga (Rp)</label>
                <input type="number" name="harga" id="edit_harga" class="form-control" required>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          <button type="submit" class="btn btn-gradient-primary">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
  /* Ensure checkboxes look standard */
  .selectItem, #selectAll { width: 18px; height: 18px; cursor: pointer; }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#barangTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
            },
            "lengthChange": false,
            "pageLength": 10,
            "autoWidth": false,
            "columnDefs": [
                { "orderable": false, "targets": [0, 5] }
            ]
        });
        
        $('#selectAll').click(function() {
            $('.selectItem').prop('checked', this.checked);
        });
        
        $('.selectItem').change(function() {
            if ($('.selectItem:checked').length == $('.selectItem').length) {
                $('#selectAll').prop('checked', true);
            } else {
                $('#selectAll').prop('checked', false);
            }
        });

        // Set Edit Modal Data
        $('.editBtn').click(function() {
            var id = $(this).data('id');
            var nama = $(this).data('nama');
            var harga = $(this).data('harga');
            
            $('#edit_id').val(id);
            $('#edit_nama').val(nama);
            $('#edit_harga').val(harga);
            
            // Set form action dynamic path for updating
             var url = '{{ route("barang.update", ":id") }}';
             url = url.replace(':id', id);
             $('#editForm').attr('action', url);
        });

        // Handle Print PDF Submission
        $('#btnCetakPdf').click(function(e) {
            e.preventDefault();
            var selected = $('.selectItem:checked');
            if(selected.length === 0) {
                alert('Silakan pilih minimal satu barang untuk dicetak.');
                return;
            }
            
            var form = $('#printPdfForm');
            
            // Set X and Y values
            var startX = $('#inputStartX').val();
            var startY = $('#inputStartY').val();
            $('#hiddenStartX').val(startX || 1);
            $('#hiddenStartY').val(startY || 1);

            // Remove previous inputs if any
            form.find('input[name="selected_items[]"]').remove();
            
            selected.each(function() {
                form.append('<input type="hidden" name="selected_items[]" value="' + $(this).val() + '">');
            });
            
            form.submit();
        });
    });
</script>
@endpush
@endsection
