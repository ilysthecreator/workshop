<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    /**
     * Redirect ke Google OAuth
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle callback dari Google
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Cari user berdasarkan id_google atau email
            $user = User::where('id_google', $googleUser->id)
                        ->orWhere('email', $googleUser->email)
                        ->first();

            if ($user) {
                // User sudah ada, update id_google jika belum ada
                if (!$user->id_google) {
                    $user->update(['id_google' => $googleUser->id]);
                }
            } else {
                // Buat user baru
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'id_google' => $googleUser->id,
                    'password' => bcrypt(Str::random(16)), // Random password
                ]);
            }

            // Generate OTP dan redirect ke halaman verifikasi OTP
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $user->update(['otp' => $otp]);

            // Kirim OTP ke email
            \Mail::raw("Kode OTP Anda adalah: $otp", function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Kode OTP Login');
            });

            // Simpan user_id di session untuk verifikasi OTP
            session(['otp_user_id' => $user->id]);

            return redirect()->route('otp.verify')
                ->with('success', 'Kode OTP telah dikirim ke email Anda.');

        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Gagal login dengan Google: ' . $e->getMessage());
        }
    }
}