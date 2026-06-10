@extends('layouts.master')

@section('content')
<div class="page-header">
  <h3 class="page-title">
    <span class="page-title-icon bg-gradient-success text-white me-2">
      <i class="mdi mdi-cash-register"></i>
    </span> Point of Sales (Kasir)
  </h3>
</div>

<div class="row">
  {{-- Form Input Barang --}}
  <div class="col-md-5 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Input Barang</h4>

        <div class="form-group">
          <label class="font-weight-bold">Kode Barang :</label>
          <input type="text" class="form-control" id="inputKodeBarang" placeholder="Masukkan kode barang, lalu tekan Enter">
        </div>

        <div class="form-group">
          <label class="font-weight-bold">Nama Barang :</label>
          <input type="text" class="form-control" id="inputNamaBarang" readonly style="background-color:#fce4c8;">
        </div>

        <div class="form-group">
          <label class="font-weight-bold">Harga Barang :</label>
          <input type="text" class="form-control" id="inputHargaBarang" readonly style="background-color:#f8d7da;">
        </div>

        <div class="form-group">
          <label class="font-weight-bold">Jumlah :</label>
          <input type="number" class="form-control" id="inputJumlah" min="1" value="1">
        </div>

        <button type="button" id="btnTambahkan" class="btn btn-gradient-success btn-fw w-100" disabled>
          Tambahkan
        </button>
      </div>
    </div>
  </div>

  {{-- Tabel Keranjang --}}
  <div class="col-md-7 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Keranjang Belanja</h4>
        <div class="table-responsive">
          <table class="table table-hover table-bordered" id="tabelKeranjang">
            <thead class="thead-dark">
              <tr>
                <th>Kode</th>
                <th>Nama</th>
                <th>Harga</th>
                <th style="width:100px;">Jumlah</th>
                <th>Subtotal</th>
                <th style="width:60px;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              {{-- Rows added dynamically --}}
            </tbody>
          </table>
        </div>

        <div class="mt-4 text-center">
          <h3 class="font-weight-bold">Total: <span id="txtTotal" class="text-success">Rp 0</span></h3>
        </div>

        <button type="button" id="btnBayar" class="btn btn-gradient-primary btn-lg btn-fw w-100 mt-3" disabled>
          <i class="mdi mdi-cash-multiple"></i> Bayar
        </button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputKode   = document.getElementById('inputKodeBarang');
    const inputNama   = document.getElementById('inputNamaBarang');
    const inputHarga  = document.getElementById('inputHargaBarang');
    const inputJumlah = document.getElementById('inputJumlah');
    const btnTambah   = document.getElementById('btnTambahkan');
    const btnBayar    = document.getElementById('btnBayar');
    const tabelBody   = document.querySelector('#tabelKeranjang tbody');
    const txtTotal    = document.getElementById('txtTotal');

    let barangDitemukan = null; // stores found barang data

    // Format Rupiah
    function formatRupiah(angka) {
        return 'Rp ' + Number(angka).toLocaleString('id-ID');
    }

    // Calculate & update grand total
    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.subtotal-val').forEach(function(el) {
            total += parseInt(el.dataset.subtotal) || 0;
        });
        txtTotal.textContent = formatRupiah(total);
        btnBayar.disabled = (tabelBody.children.length === 0);
    }

    // Update button state
    function updateBtnTambah() {
        let jumlah = parseInt(inputJumlah.value) || 0;
        btnTambah.disabled = !(barangDitemukan && jumlah > 0);
    }

    // Listen for jumlah change
    inputJumlah.addEventListener('input', updateBtnTambah);

    // Kode Barang -> Enter to search
    inputKode.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            let kode = inputKode.value.trim();
            if (!kode) return;

            axios.get('/penjualan/cari-barang/' + kode)
                .then(function(res) {
                    if (res.data.success) {
                        barangDitemukan = res.data.data;
                        inputNama.value = barangDitemukan.nama;
                        inputHarga.value = formatRupiah(barangDitemukan.harga);
                        inputJumlah.value = 1;
                        updateBtnTambah();
                        inputJumlah.focus();
                    } else {
                        barangDitemukan = null;
                        inputNama.value = '';
                        inputHarga.value = '';
                        updateBtnTambah();
                        Swal.fire('Tidak Ditemukan', 'Barang dengan kode "' + kode + '" tidak ditemukan.', 'warning');
                    }
                })
                .catch(function(err) {
                    console.error(err);
                    barangDitemukan = null;
                    inputNama.value = '';
                    inputHarga.value = '';
                    updateBtnTambah();
                });
        }
    });

    // Tambahkan to cart
    btnTambah.addEventListener('click', function() {
        if (!barangDitemukan) return;

        let kode    = barangDitemukan.id_barang;
        let nama    = barangDitemukan.nama;
        let harga   = parseInt(barangDitemukan.harga);
        let jumlah  = parseInt(inputJumlah.value) || 0;
        if (jumlah <= 0) return;

        let subtotal = harga * jumlah;

        // Check if same kode already exists in table
        let existingRow = document.querySelector('tr[data-kode="' + kode + '"]');
        if (existingRow) {
            // Update existing row
            let jmlInput = existingRow.querySelector('.jumlah-input');
            let newJumlah = parseInt(jmlInput.value) + jumlah;
            jmlInput.value = newJumlah;
            let newSubtotal = harga * newJumlah;
            let subEl = existingRow.querySelector('.subtotal-val');
            subEl.textContent = formatRupiah(newSubtotal);
            subEl.dataset.subtotal = newSubtotal;
        } else {
            // Add new row
            let tr = document.createElement('tr');
            tr.dataset.kode = kode;
            tr.dataset.harga = harga;
            tr.innerHTML = `
                <td>${kode}</td>
                <td>${nama}</td>
                <td>${formatRupiah(harga)}</td>
                <td>
                    <input type="number" class="form-control form-control-sm jumlah-input" value="${jumlah}" min="1" style="width:80px;">
                </td>
                <td class="subtotal-val" data-subtotal="${subtotal}">${formatRupiah(subtotal)}</td>
                <td>
                    <button class="btn btn-sm btn-gradient-danger btn-hapus" title="Hapus"><i class="mdi mdi-delete"></i></button>
                </td>
            `;
            tabelBody.appendChild(tr);

            // Jumlah inline edit
            tr.querySelector('.jumlah-input').addEventListener('input', function() {
                let newJml = parseInt(this.value) || 0;
                if (newJml <= 0) {
                    tr.remove();
                    updateTotal();
                    return;
                }
                let h = parseInt(tr.dataset.harga);
                let sub = h * newJml;
                tr.querySelector('.subtotal-val').textContent = formatRupiah(sub);
                tr.querySelector('.subtotal-val').dataset.subtotal = sub;
                updateTotal();
            });

            // Delete row
            tr.querySelector('.btn-hapus').addEventListener('click', function() {
                tr.remove();
                updateTotal();
            });
        }

        updateTotal();

        // Reset form
        inputKode.value = '';
        inputNama.value = '';
        inputHarga.value = '';
        inputJumlah.value = 1;
        barangDitemukan = null;
        updateBtnTambah();
        inputKode.focus();
    });

    // Bayar
    btnBayar.addEventListener('click', function() {
        let rows = tabelBody.querySelectorAll('tr');
        if (rows.length === 0) return;

        let items = [];
        let grandTotal = 0;

        rows.forEach(function(row) {
            let kode = row.dataset.kode;
            let jumlah = parseInt(row.querySelector('.jumlah-input').value) || 0;
            let subtotal = parseInt(row.querySelector('.subtotal-val').dataset.subtotal) || 0;
            items.push({
                id_barang: kode,
                jumlah: jumlah,
                subtotal: subtotal,
            });
            grandTotal += subtotal;
        });

        axios.post('/penjualan/store', {
            items: items,
            total: grandTotal,
            _token: '{{ csrf_token() }}'
        })
        .then(function(res) {
            if (res.data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Pembayaran Berhasil!',
                    text: res.data.message,
                    confirmButtonColor: '#28a745'
                });
                // Clear cart
                tabelBody.innerHTML = '';
                txtTotal.textContent = formatRupiah(0);
                btnBayar.disabled = true;
                inputKode.value = '';
                inputNama.value = '';
                inputHarga.value = '';
                inputJumlah.value = 1;
                barangDitemukan = null;
                updateBtnTambah();
            }
        })
        .catch(function(err) {
            console.error(err);
            Swal.fire('Error', 'Gagal menyimpan transaksi.', 'error');
        });
    });
});
</script>
@endpush
