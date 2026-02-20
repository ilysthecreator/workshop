@extends('layouts.auth')

@section('content')
<div class="row w-100">
    <div class="col-lg-4 mx-auto">
        <div class="auth-form-light text-left p-5">
            <div class="brand-logo">
                <img src="{{ asset('assets/images/logo.svg') }}" alt="logo">
            </div>
            <h4>Verifikasi OTP</h4>
            <h6 class="font-weight-light">Masukkan 6 karakter kode yang dikirim ke email Anda untuk melanjutkan.</h6>
            
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="pt-3" action="{{ route('otp.verify') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="otp">Kode OTP</label>
                    <input type="text" 
                           name="otp" 
                           class="form-control form-control-lg text-center font-weight-bold" 
                           id="otp" 
                           placeholder="......" 
                           maxlength="6" 
                           style="letter-spacing: 0.5rem; font-size: 1.5rem;"
                           required 
                           autofocus>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">
                        VERIFIKASI
                    </button>
                </div>
                <div class="text-center mt-4 font-weight-light"> 
                    Tidak menerima kode? <a href="#" class="text-primary">Kirim ulang</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection