@extends('layouts.master')

@section('content')
<div class="page-header">
    <h3 class="page-title"> Edit Kategori </h3>
</div>
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <form class="forms-sample" method="POST" action="{{ route('kategori.update', $kategori->idkategori) }}">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label>Nama Kategori</label>
                        <input type="text" class="form-control" name="nama_kategori" value="{{ $kategori->nama_kategori }}" required>
                    </div>
                    <button type="submit" class="btn btn-gradient-primary me-2">Update</button>
                    <a href="{{ route('kategori.index') }}" class="btn btn-light">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection