<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item nav-profile">
      <a href="#" class="nav-link">
        <div class="nav-profile-image">
          <img src="{{ asset('assets/images/faces/face1.jpg') }}" alt="profile" />
          <span class="login-status online"></span>
        </div>
        <div class="nav-profile-text d-flex flex-column">
          <span class="font-weight-bold mb-2">{{ Auth::user()->name ?? 'Guest' }}</span>
          <span class="text-secondary text-small">Admin</span>
        </div>
        <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
      </a>
    </li>
    
    <li class="nav-item {{ Request::is('/') ? 'active' : '' }}">
      <a class="nav-link" href="{{ url('/') }}">
        <span class="menu-title">Dashboard</span>
        <i class="mdi mdi-home menu-icon"></i>
      </a>
    </li>

    <li class="nav-item {{ Request::is('kategori*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ url('/kategori') }}">
        <span class="menu-title">Kategori</span>
        <i class="mdi mdi-format-list-bulleted menu-icon"></i>
      </a>
    </li>

    <li class="nav-item {{ Request::is('buku*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ url('/buku') }}">
        <span class="menu-title">Buku</span>
        <i class="mdi mdi-book-open-page-variant menu-icon"></i>
      </a>
    </li>

    <li class="nav-item {{ Request::is('barang*') ? 'active' : '' }}">
      <a class="nav-link" data-bs-toggle="collapse" href="#barang-menu" aria-expanded="false" aria-controls="barang-menu">
        <span class="menu-title">Barang</span>
        <i class="menu-arrow"></i>
        <i class="mdi mdi-tag-multiple menu-icon"></i>
      </a>
      <div class="collapse {{ Request::is('barang*') ? 'show' : '' }}" id="barang-menu">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> <a class="nav-link {{ Request::is('barang') ? 'active' : '' }}" href="{{ route('barang.index') }}">Data Barang</a></li>
          <li class="nav-item"> <a class="nav-link {{ Request::is('barang/scan') ? 'active' : '' }}" href="{{ route('barang.scan') }}">Barcode Scanner</a></li>
        </ul>
      </div>
    </li>

    <li class="nav-item {{ Request::is('wilayah*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ url('/wilayah') }}">
        <span class="menu-title">Wilayah</span>
        <i class="mdi mdi-map-marker-multiple menu-icon"></i>
      </a>
    </li>

    <li class="nav-item {{ Request::is('penjualan*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ url('/penjualan') }}">
        <span class="menu-title">POS / Kasir</span>
        <i class="mdi mdi-cash-register menu-icon"></i>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#jquery-menu" aria-expanded="false" aria-controls="jquery-menu">
        <span class="menu-title">Tugas JQuery</span>
        <i class="menu-arrow"></i>
        <i class="mdi mdi-code-tags menu-icon"></i>
      </a>
      <div class="collapse {{ Request::is('tugas-jquery*') ? 'show' : '' }}" id="jquery-menu">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> <a class="nav-link {{ Request::is('tugas-jquery/biasa') ? 'active' : '' }}" href="{{ route('tugas.jquery.biasa') }}">Versi 1 (Biasa)</a></li>
          <li class="nav-item"> <a class="nav-link {{ Request::is('tugas-jquery/datatables') ? 'active' : '' }}" href="{{ route('tugas.jquery.datatables') }}">Versi 2 (DataTables)</a></li>
        </ul>
      </div>
    </li>

    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#customer-menu" aria-expanded="false" aria-controls="customer-menu">
        <span class="menu-title">Customer</span>
        <i class="menu-arrow"></i>
        <i class="mdi mdi-account-multiple menu-icon"></i>
      </a>
      <div class="collapse {{ Request::is('customer*') ? 'show' : '' }}" id="customer-menu">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> <a class="nav-link {{ Request::is('customer') ? 'active' : '' }}" href="{{ route('customer.index') }}">Data Customer</a></li>
          <li class="nav-item"> <a class="nav-link {{ Request::is('customer/create1') ? 'active' : '' }}" href="{{ route('customer.create1') }}">Tambah Customer 1</a></li>
          <li class="nav-item"> <a class="nav-link {{ Request::is('customer/create2') ? 'active' : '' }}" href="{{ route('customer.create2') }}">Tambah Customer 2</a></li>
        </ul>
      </div>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="{{ route('kantin.index') }}">
        <span class="menu-title">Kantin Online</span>
        <i class="mdi mdi-food menu-icon"></i>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#kantin-admin" aria-expanded="false" aria-controls="kantin-admin">
        <span class="menu-title">Kelola Kantin</span>
        <i class="menu-arrow"></i>
        <i class="mdi mdi-store menu-icon"></i>
      </a>
      <div class="collapse {{ Request::is('vendor*') ? 'show' : '' }}" id="kantin-admin">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> <a class="nav-link" href="{{ route('vendor.index') }}">Master Vendor</a></li>
          <li class="nav-item"> <a class="nav-link" href="{{ route('vendor.menu') }}">Master Menu</a></li>
          <li class="nav-item"> <a class="nav-link" href="{{ route('vendor.pesanan') }}">Data Pesanan</a></li>
          <li class="nav-item"> <a class="nav-link {{ Request::is('vendor/scan') ? 'active' : '' }}" href="{{ route('vendor.scan') }}"><i class="mdi mdi-qrcode-scan"></i> Scan QR Pesanan</a></li>
        </ul>
      </div>
    </li>

    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#kunjungan-toko" aria-expanded="false" aria-controls="kunjungan-toko">
        <span class="menu-title">Kunjungan Toko</span>
        <i class="menu-arrow"></i>
        <i class="mdi mdi-map-marker-radius menu-icon"></i>
      </a>
      <div class="collapse {{ Request::is('kunjungan-toko*') ? 'show' : '' }}" id="kunjungan-toko">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> <a class="nav-link {{ Request::is('kunjungan-toko') ? 'active' : '' }}" href="{{ route('kunjungan-toko.index') }}">List Toko</a></li>
          <li class="nav-item"> <a class="nav-link {{ Request::is('kunjungan-toko/scan') ? 'active' : '' }}" href="{{ route('kunjungan-toko.scan') }}">Titik Kunjungan (Sales)</a></li>
        </ul>
      </div>
    </li>

    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#antrian-sse" aria-expanded="false" aria-controls="antrian-sse">
        <span class="menu-title">Antrian SSE</span>
        <i class="menu-arrow"></i>
        <i class="mdi mdi-broadcast menu-icon"></i>
      </a>
      <div class="collapse" id="antrian-sse">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> <a class="nav-link" href="{{ route('antrian.guest') }}" target="_blank">Guest (Daftar)</a></li>
          <li class="nav-item"> <a class="nav-link" href="{{ route('antrian.admin') }}">Admin</a></li>
          <li class="nav-item"> <a class="nav-link" href="{{ route('antrian.papan') }}" target="_blank">Papan Antrian</a></li>
        </ul>
      </div>
    </li>

    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#nfc-absensi-menu" aria-expanded="{{ Request::is('nfc-absensi*') ? 'true' : 'false' }}" aria-controls="nfc-absensi-menu">
        <span class="menu-title">Absensi NFC</span>
        <i class="menu-arrow"></i>
        <i class="mdi mdi-nfc menu-icon"></i>
      </a>
      <div class="collapse {{ Request::is('nfc-absensi*') ? 'show' : '' }}" id="nfc-absensi-menu">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> <a class="nav-link {{ Request::is('nfc-absensi/students') ? 'active' : '' }}" href="{{ route('nfc.students.index') }}">Kelola Mahasiswa</a></li>
          <li class="nav-item"> <a class="nav-link {{ Request::is('nfc-absensi/scan') ? 'active' : '' }}" href="{{ route('nfc.scan.index') }}">Scanner Mobile</a></li>
          <li class="nav-item"> <a class="nav-link {{ Request::is('nfc-absensi/history') ? 'active' : '' }}" href="{{ route('nfc.history.index') }}">Riwayat Absensi</a></li>
        </ul>
      </div>
    </li>

  </ul>
</nav>