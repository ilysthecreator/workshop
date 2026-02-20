@extends('layouts.master')

@section('content')
<div class="page-header">
  <h3 class="page-title">
    <span class="page-title-icon bg-gradient-primary text-white me-2">
      <i class="mdi mdi-home"></i>
    </span> Dashboard
  </h3>
</div>

<div class="row">
  <div class="col-md-4 stretch-card grid-margin">
    <div class="card bg-gradient-danger card-img-holder text-white">
      <div class="card-body">
        <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
        <h4 class="font-weight-normal mb-3">Total Buku <i class="mdi mdi-book-open-page-variant mdi-24px float-right"></i>
        </h4>
        <h2 class="mb-5">{{ $buku }}</h2>
        <h6 class="card-text">Jumlah buku tersedia</h6>
      </div>
    </div>
  </div>

  <div class="col-md-4 stretch-card grid-margin">
    <div class="card bg-gradient-info card-img-holder text-white">
      <div class="card-body">
        <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
        <h4 class="font-weight-normal mb-3">Total Kategori <i class="mdi mdi-format-list-bulleted mdi-24px float-right"></i>
        </h4>
        <h2 class="mb-5">{{ $kategori }}</h2>
        <h6 class="card-text">Kategori buku terdaftar</h6>
      </div>
    </div>
  </div>

  <div class="col-md-4 stretch-card grid-margin">
    <div class="card bg-gradient-success card-img-holder text-white">
      <div class="card-body">
        <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
        <h4 class="font-weight-normal mb-3">Generator <i class="mdi mdi-file-pdf mdi-24px float-right"></i>
        </h4>
        <h2 class="mb-5">2 Fitur</h2>
        <h6 class="card-text">Sertifikat & Undangan</h6>
      </div>
    </div>
  </div>
</div>
@endsection