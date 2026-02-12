@extends('layouts.master')

@section('content')
<div class="page-header">
  <h3 class="page-title">
    <span class="page-title-icon bg-gradient-primary text-white me-2">
      <i class="mdi mdi-format-list-bulleted"></i>
    </span> Master Kategori
  </h3>
  <nav aria-label="breadcrumb">
    <a href="{{ route('kategori.create') }}" class="btn btn-gradient-primary btn-fw">+ Tambah Kategori</a>
  </nav>
</div>

<div class="row">
  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Daftar Kategori Buku</h4>
        <table class="table table-hover">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nama Kategori</th>
            </tr>
          </thead>
          <tbody>
            @foreach($kategori as $item)
            <tr>
              <td>{{ $item->idkategori }}</td>
              <td>{{ $item->nama_kategori }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection