@extends('layouts.master')

@section('content')
<div class="page-header">
  <h3 class="page-title">
    <span class="page-title-icon bg-gradient-primary text-white me-2">
      <i class="mdi mdi-store"></i>
    </span> Master Data Vendor
  </h3>
</div>

<div class="row">
  <div class="col-md-4 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Tambah Vendor Baru</h4>
        <form method="POST" action="{{ route('vendor.store') }}">
            @csrf
            <div class="form-group">
                <label>Nama Vendor</label>
                <input type="text" class="form-control" name="nama_vendor" required placeholder="Cth: Kantin Bu Ani">
            </div>
            <button type="submit" class="btn btn-gradient-primary w-100">Simpan Vendor</button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-md-8 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Daftar Vendor Tersedia</h4>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="table-responsive">
          <table class="table">
            <thead class="thead-dark">
              <tr>
                <th>ID</th>
                <th>Nama Vendor</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($vendors as $v)
              <tr>
                <td>{{ $v->idvendor }}</td>
                <td>{{ $v->nama_vendor }}</td>
                <td>
                    <form action="{{ route('vendor.destroy', $v->idvendor) }}" method="POST" onsubmit="return confirm('Yakin hapus vendor ini? Semua menu akan terhapus.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus"><i class="mdi mdi-delete"></i></button>
                    </form>
                </td>
              </tr>
              @empty
              <tr><td colspan="3" class="text-center">Belum ada vendor terdaftar</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
