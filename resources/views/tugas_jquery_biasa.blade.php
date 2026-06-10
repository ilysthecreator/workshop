@extends('layouts.master')

@push('styles')
<style>
    /* Tugas 3a: Kursor di row tabel */
    #tabelBarang tbody tr:hover {
        cursor: pointer;
        background-color: #f4f4f4;
    }

    /* Custom CSS agar Select2 & Select Biasa rapi */
    .select2-container--default .select2-selection--single {
        border: 1px solid #ebedf2;
        height: 50px;
        padding: 10px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 48px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: normal;
        padding-left: 0;
        color: #495057;
    }
    .select-kota-container {
        transition: all 0.3s ease;
    }
    .select-kota-container:hover {
        transform: translateY(-2px);
    }
    .badge-kota {
        background: linear-gradient(to right, #198ae3, #198ae3);
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(25, 138, 227, 0.2);
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <h3 class="page-title"> Tugas JQuery - Versi 1 (Tabel Biasa) </h3>
</div>

<div class="row">
    <div class="col-md-4 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Tambah Barang</h4>
                <form id="formTambahBarang">
                    <div class="form-group">
                        <label>Nama Barang</label>
                        <input type="text" class="form-control" id="inputNama" required>
                    </div>
                    <div class="form-group">
                        <label>Harga Barang</label>
                        <input type="number" class="form-control" id="inputHarga" required>
                    </div>
                    <!-- Tugas 1: Button type button and check HTML5 validity -->
                    <button type="button" id="btnSubmitBarang" class="btn btn-gradient-primary w-100">Submit</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Daftar Barang (Tabel HTML Biasa)</h4>
                <div class="table-responsive">
                    <table class="table table-striped" id="tabelBarang">
                        <thead>
                            <tr>
                                <th>ID Barang</th>
                                <th>Nama</th>
                                <th>Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data ditambahkan via JQuery append -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title text-primary"><i class="mdi mdi-map-marker-radius"></i> Manipulasi Select Kota</h4>
                <p class="card-description">Tambahkan kota baru secara dinamis dan pilih dari dropdown versi standar atau Select2.</p>
                
                <div class="row mb-5 align-items-center">
                    <div class="col-md-7">
                        <div class="form-group mb-0">
                            <label class="font-weight-bold text-muted">TAMBAH KOTA BARU</label>
                            <div class="input-group">
                                <span class="input-group-text bg-gradient-info text-white border-0"><i class="mdi mdi-city"></i></span>
                                <input type="text" id="inputKotaBaru" class="form-control" placeholder="Ketik nama kota...">
                                <button class="btn btn-gradient-info btn-icon-text" id="btnTambahKota" type="button">
                                    <i class="mdi mdi-plus btn-icon-prepend"></i> Tambah
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="badge-kota d-flex align-items-center justify-content-between">
                            <span class="small font-weight-bold">STATUS PEMILIHAN:</span>
                            <span id="teksKotaTerpilih" class="mb-0 h5 font-weight-bold">-</span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4 mb-md-0">
                        <div class="select-kota-container p-4 rounded border bg-white shadow-sm h-100">
                            <h6 class="text-secondary mb-3 font-weight-bold border-bottom pb-2">
                                <i class="mdi mdi-select"></i> VERSI STANDAR
                            </h6>
                            <div class="form-group mb-0">
                                <select id="selectBiasa" class="form-control form-control-lg select-kota border">
                                    <option value="">-- Pilih Kota --</option>
                                    <option value="Jakarta">Jakarta</option>
                                    <option value="Bandung">Bandung</option>
                                </select>
                            </div>
                            <small class="text-muted mt-2 d-block">Elemen &lt;select&gt; standar HTML5.</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="select-kota-container p-4 rounded border bg-white shadow-sm h-100">
                            <h6 class="text-secondary mb-3 font-weight-bold border-bottom pb-2">
                                <i class="mdi mdi-layers-outline"></i> VERSI SELECT2
                            </h6>
                            <div class="form-group mb-0">
                                <select id="selectDua" class="form-control select-kota" style="width:100%">
                                    <option value="">-- Pilih Kota --</option>
                                    <option value="Jakarta">Jakarta</option>
                                    <option value="Bandung">Bandung</option>
                                </select>
                            </div>
                            <small class="text-muted mt-2 d-block">Library Select2 dengan fitur pencarian.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Update/Delete -->
<div class="modal fade" id="modalActionBarang" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Kelola Data Barang</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formModalBarang">
            <div class="form-group">
                <label>ID Barang</label>
                <!-- Tugas 3: ID Barang Readonly -->
                <input type="text" class="form-control" id="modalId" readonly>
            </div>
            <div class="form-group">
                <label>Nama Barang</label>
                <input type="text" class="form-control" id="modalNama" required>
            </div>
            <div class="form-group">
                <label>Harga Barang</label>
                <input type="number" class="form-control" id="modalHarga" required>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" id="btnHapusBarang">Hapus</button>
        <button type="button" class="btn btn-success" id="btnUbahBarang">Ubah</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Inisialisasi Select2
    $('#selectDua').select2();

    let idCounter = 1;
    let selectedRow; // Untuk menyimpan referensi baris <tr> yang diklik

    // TUGAS 1 & 2: Form Submit with Spinner & Append to Table
    $('#btnSubmitBarang').click(function() {
        let form = document.getElementById('formTambahBarang');
        
        // Tugas 1: Validasi HTML5
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        let nama = $('#inputNama').val();
        let harga = $('#inputHarga').val();
        let btn = $(this);

        // Tugas 1: Efek Spinner & Disable
        let originalText = btn.text();
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Memproses...');

        // Simulasi proses delay 1 detik
        setTimeout(function() {
            let idBarang = 'BRG-' + idCounter.toString().padStart(3, '0');
            
            // Tugas 2: Append baris baru ke tbody HTML biasa
            let newRow = `
                <tr data-id="${idBarang}">
                    <td class="td-id">${idBarang}</td>
                    <td class="td-nama">${nama}</td>
                    <td class="td-harga">${harga}</td>
                </tr>
            `;
            $('#tabelBarang tbody').append(newRow);

            idCounter++;

            // Reset form dan tombol
            $('#inputNama').val('');
            $('#inputHarga').val('');
            btn.prop('disabled', false).text(originalText);
        }, 1000);
    });

    // TUGAS 3: Hover & Click Baris Tabel untuk Munculkan Modal
    // Menggunakan delegasi event karena baris ditambahkan secara dinamis
    $('#tabelBarang tbody').on('click', 'tr', function() {
        selectedRow = $(this);
        let id = selectedRow.find('.td-id').text();
        let nama = selectedRow.find('.td-nama').text();
        let harga = selectedRow.find('.td-harga').text();

        // Isi data ke modal
        $('#modalId').val(id);
        $('#modalNama').val(nama);
        $('#modalHarga').val(harga);

        // Munculkan Modal
        $('#modalActionBarang').modal('show');
    });

    // TUGAS 3: Ubah Data (Update)
    $('#btnUbahBarang').click(function() {
        let form = document.getElementById('formModalBarang');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        let btn = $(this);
        let originalText = btn.text();
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');

        setTimeout(function() {
            let namaBaru = $('#modalNama').val();
            let hargaBaru = $('#modalHarga').val();

            // Update teks pada baris tabel yang sebelumnya diklik
            selectedRow.find('.td-nama').text(namaBaru);
            selectedRow.find('.td-harga').text(hargaBaru);

            btn.prop('disabled', false).text(originalText);
            $('#modalActionBarang').modal('hide');
        }, 1000);
    });

    // TUGAS 3: Hapus Data (Delete)
    $('#btnHapusBarang').click(function() {
        let btn = $(this);
        let originalText = btn.text();
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menghapus...');

        setTimeout(function() {
            // Hapus baris dari DOM
            selectedRow.remove();

            btn.prop('disabled', false).text(originalText);
            $('#modalActionBarang').modal('hide');
        }, 1000);
    });

    // TUGAS 4: Manipulasi Dropdown & Select2
    $('#btnTambahKota').click(function() {
        let kotaBaru = $('#inputKotaBaru').val().trim();
        
        if (kotaBaru !== '') {
            // Tambah option ke Select Biasa
            $('#selectBiasa').append(new Option(kotaBaru, kotaBaru));
            
            // Tambah option ke Select2
            let newOption = new Option(kotaBaru, kotaBaru, false, false);
            $('#selectDua').append(newOption).trigger('change');
            
            $('#inputKotaBaru').val('');
        } else {
            alert('Masukkan nama kota!');
        }
    });

    $('.select-kota').change(function() {
        let val = $(this).val();
        $('#teksKotaTerpilih').text(val ? val : "-");
        
        // Sinkronisasi (Opsional)
        $('.select-kota').not(this).val(val).trigger('change.select2');
    });
});
</script>
@endpush
