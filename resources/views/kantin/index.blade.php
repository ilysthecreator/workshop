@extends('layouts.master')

@section('content')
<div class="page-header">
  <h3 class="page-title">
    <span class="page-title-icon bg-gradient-success text-white me-2">
      <i class="mdi mdi-food"></i>
    </span> Kantin Online
  </h3>
</div>

<div class="row">
  {{-- Form Input Menu --}}
  <div class="col-md-5 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Pesan Menu</h4>

        <div class="form-group">
          <label class="font-weight-bold">Pilih Vendor :</label>
          <select class="form-control" id="selectVendor">
            <option value="">-- Pilih Vendor --</option>
            @foreach($vendors as $vendor)
            <option value="{{ $vendor->idvendor }}">{{ $vendor->nama_vendor }}</option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <label class="font-weight-bold">Pilih Menu :</label>
          <select class="form-control" id="selectMenu" disabled>
            <option value="">-- Pilih Menu --</option>
          </select>
        </div>

        <div class="form-group">
          <label class="font-weight-bold">Jumlah :</label>
          <input type="number" class="form-control" id="inputJumlah" min="1" value="1">
        </div>

        <div class="form-group">
          <label class="font-weight-bold">Catatan :</label>
          <input type="text" class="form-control" id="inputCatatan" placeholder="Cth: Pedas, tanpa msg">
        </div>

        <button type="button" id="btnTambahkan" class="btn btn-gradient-success btn-fw w-100" disabled>
          Tambahkan ke Keranjang
        </button>
      </div>
    </div>
  </div>

  {{-- Tabel Keranjang --}}
  <div class="col-md-7 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Keranjang Anda</h4>
        <div class="table-responsive">
          <table class="table table-hover table-bordered" id="tabelKeranjang">
            <thead class="thead-dark">
              <tr>
                <th>Menu</th>
                <th>Harga</th>
                <th style="width:100px;">Qty</th>
                <th>Catatan</th>
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
          <h3 class="font-weight-bold">Total: Rp <span id="txtTotal">0</span></h3>
        </div>

        <button type="button" id="btnBayar" class="btn btn-gradient-primary btn-lg btn-fw w-100 mt-3" disabled>
          <i class="mdi mdi-credit-card"></i> Pesan & Bayar
        </button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
{{-- Midtrans Snap.js --}}
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selVendor   = document.getElementById('selectVendor');
    const selMenu     = document.getElementById('selectMenu');
    const inputJumlah = document.getElementById('inputJumlah');
    const inputCatatan= document.getElementById('inputCatatan');
    const btnTambah   = document.getElementById('btnTambahkan');
    const btnBayar    = document.getElementById('btnBayar');
    const tabelBody   = document.querySelector('#tabelKeranjang tbody');
    const txtTotal    = document.getElementById('txtTotal');

    let menuList = [];

    // Format Rupiah Number only
    function formatAngka(angka) {
        return Number(angka).toLocaleString('id-ID');
    }

    // Update grand total
    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.subtotal-val').forEach(function(el) {
            total += parseInt(el.dataset.subtotal) || 0;
        });
        txtTotal.textContent = formatAngka(total);
        btnBayar.disabled = (tabelBody.children.length === 0);
    }

    // Update btn add state
    function checkAddState() {
        btnTambah.disabled = !(selMenu.value !== '' && inputJumlah.value > 0);
    }
    selMenu.addEventListener('change', checkAddState);
    inputJumlah.addEventListener('input', checkAddState);

    // Cascading Vendor -> Menu
    selVendor.addEventListener('change', function() {
        let idvendor = this.value;
        selMenu.innerHTML = '<option value="">-- Pilih Menu --</option>';
        selMenu.disabled = true;
        menuList = [];
        checkAddState();

        if (idvendor) {
            axios.get('/kantin/menu/' + idvendor)
                .then(res => {
                    if(res.data.success) {
                        menuList = res.data.data;
                        menuList.forEach(m => {
                            let opt = document.createElement('option');
                            opt.value = m.idmenu;
                            opt.dataset.harga = m.harga;
                            opt.textContent = m.nama_menu + ' - Rp ' + formatAngka(m.harga);
                            selMenu.appendChild(opt);
                        });
                        if (menuList.length > 0) selMenu.disabled = false;
                    }
                });
        }
    });

    // Tambah ke keranjang
    btnTambah.addEventListener('click', function() {
        let idmenu = selMenu.value;
        let opt = selMenu.options[selMenu.selectedIndex];
        let nama_menu = opt.text.split(' - Rp')[0];
        let harga = parseInt(opt.dataset.harga);
        let jumlah = parseInt(inputJumlah.value) || 1;
        let catatan = inputCatatan.value;
        let subtotal = harga * jumlah;

        // Bikin Row baru (Tiap item yang beda catatan dipisah saja biar mudah)
        let tr = document.createElement('tr');
        tr.dataset.idmenu = idmenu;
        tr.dataset.jumlah = jumlah;
        tr.dataset.harga = harga;
        tr.dataset.catatan = catatan;

        tr.innerHTML = `
            <td>${nama_menu}</td>
            <td>Rp ${formatAngka(harga)}</td>
            <td>${jumlah}</td>
            <td>${catatan}</td>
            <td class="subtotal-val" data-subtotal="${subtotal}">Rp ${formatAngka(subtotal)}</td>
            <td>
                <button class="btn btn-sm btn-gradient-danger btn-hapus" title="Hapus"><i class="mdi mdi-delete"></i></button>
            </td>
        `;

        tabelBody.appendChild(tr);
        
        tr.querySelector('.btn-hapus').addEventListener('click', function() {
            tr.remove();
            updateTotal();
        });

        updateTotal();

        // rest
        selMenu.value = '';
        inputJumlah.value = 1;
        inputCatatan.value = '';
        checkAddState();
    });

    // Proses Pembayaran ke Midtrans
    btnBayar.addEventListener('click', function() {
        let rows = tabelBody.querySelectorAll('tr');
        if (rows.length === 0) return;

        let items = [];
        let grandTotal = 0;

        rows.forEach(function(row) {
            let subtotal = parseInt(row.querySelector('.subtotal-val').dataset.subtotal);
            items.push({
                idmenu: row.dataset.idmenu,
                jumlah: parseInt(row.dataset.jumlah),
                harga: parseInt(row.dataset.harga),
                catatan: row.dataset.catatan,
                subtotal: subtotal
            });
            grandTotal += subtotal;
        });

        btnBayar.disabled = true;
        btnBayar.innerHTML = 'Memproses...';

        axios.post('/kantin/store', {
            items: items,
            total: grandTotal,
            _token: '{{ csrf_token() }}'
        })
        .then(function(res) {
            if (res.data.success) {
                // Panggil Snap Midtrans
                window.snap.pay(res.data.snap_token, {
                    onSuccess: function(result) {
                        Swal.fire({
                            title: 'Sukses!',
                            icon: 'success',
                            html: 'Pembayaran berhasil!<br><br><span style="color:#e74c3c; font-weight:bold; font-size:14px;">PERHATIAN TANGKAP LAYAR (SCREENSHOT)</span><br><span style="font-size:13px;">Di halaman selanjutnya, mohon screenshot QR Code Anda untuk diserahkan ke Kasir.</span>',
                            confirmButtonText: 'Buka Halaman QR Code'
                        }).then(() => {
                            window.location.href = '/kantin/success/' + res.data.order_id;
                        });
                    },
                    onPending: function(result) {
                        Swal.fire({
                            title: 'Menunggu',
                            icon: 'info',
                            html: 'Silahkan selesaikan pembayaran anda.<br><br><span style="color:#e74c3c; font-weight:bold; font-size:14px;">PERHATIAN TANGKAP LAYAR (SCREENSHOT)</span><br><span style="font-size:13px;">Di halaman selanjutnya, mohon screenshot QR Code Anda untuk diserahkan ke Kasir nanti.</span>',
                            confirmButtonText: 'Buka Halaman QR Code'
                        }).then(() => {
                            window.location.href = '/kantin/success/' + res.data.order_id;
                        });
                    },
                    onError: function(result) {
                        Swal.fire('Gagal!', 'Pembayaran gagal!', 'error');
                        btnBayar.disabled = false;
                        btnBayar.innerHTML = '<i class="mdi mdi-credit-card"></i> Pesan & Bayar';
                    },
                    onClose: function() {
                        Swal.fire('Perhatian', 'Anda menutup popup pembayaran sebelum menyelesaikannya', 'warning');
                        btnBayar.disabled = false;
                        btnBayar.innerHTML = '<i class="mdi mdi-credit-card"></i> Pesan & Bayar';
                    }
                });
            } else {
                Swal.fire('Error', res.data.message, 'error');
                btnBayar.disabled = false;
                btnBayar.innerHTML = '<i class="mdi mdi-credit-card"></i> Pesan & Bayar';
            }
        })
        .catch(function(err) {
            console.error(err);
            let errMsg = 'Terjadi kesalahan sistem.';
            if (err.response && err.response.data && err.response.data.message) {
                errMsg = err.response.data.message;
            }
            Swal.fire('Error', errMsg, 'error');
            btnBayar.disabled = false;
            btnBayar.innerHTML = '<i class="mdi mdi-credit-card"></i> Pesan & Bayar';
        });
    });
});
</script>
@endpush
