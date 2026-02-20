<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    // 1. Redirect ke Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // 2. Handle Callback (Simpan User & Kirim OTP)
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Cari user berdasarkan id_google atau email
            $user = User::where('id_google', $googleUser->id)
                        ->orWhere('email', $googleUser->email)
                        ->first();

            if ($user) {
                // Update Google ID jika belum ada
                if (!$user->id_google) {
                    $user->update(['id_google' => $googleUser->id]);
                }
            } else {
                // Buat User Baru
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'id_google' => $googleUser->id,
                    'password' => bcrypt(Str::random(16)), // Password acak
                ]);
            }

            // Generate OTP 6 Karakter & Simpan ke DB
            $otp = strtoupper(Str::random(6));
            $user->update(['otp' => $otp]);

            // Kirim Email OTP
            Mail::raw("Kode OTP Login Anda: $otp", function ($message) use ($user) {
                $message->to($user->email)->subject('Kode OTP Keamanan');
            });

            // Simpan ID sementara di session
            session(['otp_user_id' => $user->id]);

            return redirect()->route('otp.verify')->with('success', 'Kode OTP telah dikirim ke email Anda.');

        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Gagal login Google: ' . $e->getMessage());
        }
    }

    // 3. Tampilkan Form OTP
    public function showOtpForm()
    {
        if (!session()->has('otp_user_id')) {
            return redirect('/login');
        }
        return view('auth.otp');
    }

    // 4. Proses Verifikasi OTP
    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|string|size:6']);
        
        $user = User::find(session('otp_user_id'));

        if (!$user || $user->otp !== $request->otp) {
            return back()->withErrors(['otp' => 'Kode OTP salah atau kadaluwarsa.']);
        }

        // Reset OTP & Login
        $user->update(['otp' => null]);
        Auth::login($user);
        session()->forget('otp_user_id');

        return redirect('/home'); // Masuk ke Dashboard
    }
}