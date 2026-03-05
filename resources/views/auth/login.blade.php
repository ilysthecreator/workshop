@extends('layouts.auth')

@section('content')
<style>
    /* Custom light styles matching the layout but with a white background */
    .auth .auth-form-dark-custom {
        background: #ffffff; /* White background */
        border-radius: 12px;
        color: #3f3f46;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1); /* softer lighter shadow */
        border: 1px solid #e4e4e7;
    }
    .auth-form-dark-custom .brand-logo img {
        width: 45px;
        border-radius: 8px;
    }
    .auth-form-dark-custom h4 {
        font-weight: 700;
        margin-top: 1rem;
        font-size: 1.5rem;
        color: #18181b; /* Dark text */
    }
    .auth-form-dark-custom h6 {
        color: #71717a;
        font-size: 0.95rem;
    }
    .auth-form-dark-custom h6 a {
        color: #18181b; /* Dark text for link */
        text-decoration: underline !important;
        text-decoration-thickness: 1px;
        text-underline-offset: 4px;
        font-weight: 500;
    }
    .auth-form-dark-custom .form-control {
        background: #f4f4f5 !important; /* light input background */
        border: 1px solid #e4e4e7 !important; /* light border */
        color: #3f3f46 !important;
        border-radius: 6px;
        padding-left: 2.8rem;
        font-size: 0.95rem;
        height: 3rem;
    }
    .auth-form-dark-custom .form-control:focus {
        border-color: #b66dff !important;
        background: #ffffff !important;
        box-shadow: 0 0 0 0.2rem rgba(182, 109, 255, 0.25) !important;
    }
    .auth-form-dark-custom .input-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #a1a1aa;
        font-size: 1.2rem;
        z-index: 4;
    }
    .auth-form-dark-custom .btn-gradient-primary {
        background: #b66dff !important; /* Bright purple */
        background-image: none !important; /* removing gradient */
        border: none;
        color: #ffffff;
        font-weight: 600;
        border-radius: 6px;
        height: 3rem;
        font-size: 1rem;
    }
    .divider {
        display: flex;
        align-items: center;
        text-align: center;
        color: #71717a;
        font-size: 0.75rem;
        font-weight: 500;
        margin: 1.5rem 0;
        text-transform: uppercase;
    }
    .divider::before, .divider::after {
        content: '';
        flex: 1;
        border-bottom: 1px solid #27272a;
    }
    .divider:not(:empty)::before {
        margin-right: .75em;
    }
    .divider:not(:empty)::after {
        margin-left: .75em;
    }
    .social-btn {
        background: #f4f4f5; /* light background for buttons */
        border: 1px solid #e4e4e7;
        color: #18181b; /* dark text/icon */
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.6rem;
        height: 3rem;
        transition: all 0.2s ease-in-out;
    }
    .social-btn:hover {
        background: #e4e4e7;
        color: #18181b;
        text-decoration: none;
    }
    .social-btn i {
        font-size: 1.3rem;
    }
    .x-icon {
        font-weight: 600;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        font-size: 1.1rem;
        text-decoration: underline;
        text-decoration-thickness: 1px;
        text-underline-offset: 4px;
        color: #18181b !important;
    }
    .circle-icon-top {
        width: 20px;
        height: 20px;
        background-color: #e4e4e7; /* dark grey in light mode */
        border-radius: 50%;
        margin: 0 auto 1.5rem auto;
    }
</style>

<div class="row flex-grow w-100 m-0">
    <div class="col-lg-4 mx-auto align-self-center">
        <div class="auth-form-dark-custom text-center p-4 p-sm-5">
            <div class="brand-logo mb-4 d-flex justify-content-center">
                <img src="{{ asset('assets/images/logo.svg') }}" alt="logo" style="width: auto; max-height: 40px; display: block;">
            </div>
            <h4>Welcome Back</h4>
            <h6 class="font-weight-light mb-4 pb-2">Don't have an account yet? <a href="{{ route('register') }}">Sign up</a></h6>
            
            @if (session('error'))
                <div class="alert alert-danger text-left">
                    {{ session('error') }}
                </div>
            @endif

            <form class="text-left" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group position-relative mb-3">
                    <i class="mdi mdi-email-outline input-icon"></i>
                    <input type="email" name="email" class="form-control form-control-lg @error('email') is-invalid @enderror" placeholder="email address" value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <span class="invalid-feedback" role="alert" style="display:block;">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group position-relative mb-4">
                    <i class="mdi mdi-lock-outline input-icon"></i>
                    <input type="password" name="password" class="form-control form-control-lg @error('password') is-invalid @enderror" placeholder="Password" required>
                    @error('password')
                        <span class="invalid-feedback" role="alert" style="display:block;">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="mt-2">
                    <button type="submit" class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn text-white w-100">Login</button>
                </div>
                
                <div class="divider">OR</div>

                <div class="d-flex justify-content-center mt-3">
                    <a href="{{ route('google.login') ?? 'javascript:void(0)' }}" class="social-btn w-100">
                        <svg style="width: 22px; height: 22px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                        </svg>
                        <span class="ml-2 font-weight-medium" style="margin-left: 10px; color: #18181b;">Continue with Google</span>
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection