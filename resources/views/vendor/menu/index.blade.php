@extends('layouts.master')

@section('content')
<div class="page-header">
  <h3 class="page-title">
    <span class="page-title-icon bg-gradient-info text-white me-2">
      <i class="mdi mdi-view-list"></i>
    </span> Master Data Menu Kantin
  </h3>
</div>

<div class="row">
  <div class="col-md-4 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Tambah Menu</h4>
        <form method="POST" action="{{ route('vendor.menu.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label>Pilih Vendor Asal</label>
                <select class="form-control" name="idvendor" required>
                    <option value="">-- Pilih Vendor --</option>
                    @foreach($vendors as $v)
                    <option value="{{ $v->idvendor }}">{{ $v->nama_vendor }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Nama Menu</label>
                <input type="text" class="form-control" name="nama_menu" required>
            </div>
            <div class="form-group">
                <label>Harga (Rp)</label>
                <input type="number" class="form-control" name="harga" required>
            </div>
            <div class="form-group">
                <label>Foto Menu (Opsional)</label>
                <input type="file" class="form-control" name="gambar" accept="image/*">
            </div>
            <button type="submit" class="btn btn-gradient-primary w-100">Simpan Menu</button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-md-8 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Daftar Semua Menu</h4>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>Foto</th>
                <th>Nama Vendor</th>
                <th>Nama Menu</th>
                <th>Harga</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($menus as $m)
              <tr>
                <td>
                    @if($m->path_gambar)
                    <img src="{{ asset('storage/' . $m->path_gambar) }}" alt="menu" style="border-radius:0; width:50px; height:50px; object-fit:cover;">
                    @else
                    <div class="bg-secondary text-white text-center rounded d-flex align-items-center justify-content-center" style="width:50px; height:50px; font-size:10px;">No Img</div>
                    @endif
                </td>
                <td><span class="badge badge-info">{{ $m->vendor->nama_vendor ?? '-' }}</span></td>
                <td>{{ $m->nama_menu }}</td>
                <td>Rp {{ number_format($m->harga, 0, ',', '.') }}</td>
                <td>
                    <form action="{{ route('vendor.menu.destroy', $m->idmenu) }}" method="POST" onsubmit="return confirm('Yakin hapus?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger"><i class="mdi mdi-delete"></i></button>
                    </form>
                </td>
              </tr>
              @empty
              <tr><td colspan="5" class="text-center">Belum ada menu</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
