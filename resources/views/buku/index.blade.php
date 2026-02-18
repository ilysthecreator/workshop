@extends('layouts.master')

@section('content')
<div class="page-header">
  <h3 class="page-title">
    <span class="page-title-icon bg-gradient-primary text-white me-2">
      <i class="mdi mdi-book-open-page-variant"></i>
    </span> Master Buku
  </h3>
  <nav aria-label="breadcrumb">
    <a href="{{ route('buku.create') }}" class="btn btn-gradient-primary btn-fw">+ Tambah Buku</a>
  </nav>
</div>

<div class="row">
  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Koleksi Buku Perpustakaan</h4>
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Kode</th>
                <th>Judul</th>
                <th>Pengarang</th>
                <th>Kategori</th>
                <th width="15%">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach($buku as $item)
              <tr>
                <td><strong>{{ $item->kode }}</strong></td>
                <td>{{ $item->judul }}</td>
                <td>{{ $item->pengarang }}</td>
                <td>
                  <label class="badge badge-gradient-info">
                    {{ $item->kategori->nama_kategori ?? 'Tanpa Kategori' }}
                  </label>
                </td>
                <td>
                  <form action="{{ route('buku.destroy', $item->idbuku) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                    <a href="{{ route('buku.edit', $item->idbuku) }}" class="btn btn-sm btn-gradient-warning">Edit</a>
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-gradient-danger">Hapus</button>
                  </form>
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
@endsection