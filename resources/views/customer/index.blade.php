@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Data Customer</h4>
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Foto</th>
                                <th>Nama</th>
                                <th>Alamat Lengkap</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customers as $index => $c)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    @if($c->foto_blob)
                                        <img src="data:image/jpeg;base64,{{ base64_encode($c->foto_blob) }}" alt="Foto Customer" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                                        <br><small class="text-muted">(BLOB)</small>
                                    @elseif($c->foto_path)
                                        <img src="{{ asset('storage/' . $c->foto_path) }}" alt="Foto Customer" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                                        <br><small class="text-muted">(FILE)</small>
                                    @else
                                        <span class="text-muted">Tidak ada foto</span>
                                    @endif
                                </td>
                                <td>{{ $c->nama }}</td>
                                <td style="white-space: normal;">
                                    {{ $c->alamat }}<br>
                                    <small class="text-muted">
                                        {{ $c->village->name ?? '' }}, 
                                        {{ $c->district->name ?? '' }}, 
                                        {{ $c->regency->name ?? '' }}, 
                                        {{ $c->province->name ?? '' }}
                                    </small>
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
