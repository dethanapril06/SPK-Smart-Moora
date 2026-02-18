<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Proses login (dengan rate limiting seperti Breeze)
     */
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Rate limiting: max 5 percobaan per menit
        $this->ensureIsNotRateLimited($request);

        // Coba login
        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            // Increment rate limiter on failed attempt
            RateLimiter::hit($this->throttleKey($request));

            throw ValidationException::withMessages([
                'email' => 'Email atau password salah.',
            ]);
        }

        // Reset rate limiter on successful login
        RateLimiter::clear($this->throttleKey($request));

        // Regenerate session to prevent session fixation attacks
        $request->session()->regenerate();

        $user = Auth::user();

        // Redirect berdasarkan level
        if ($user->isAdmin()) {
            return redirect()->intended('/admin/dashboard')
                ->with('success', 'Selamat datang, ' . $user->name . '!');
        } elseif ($user->isWaliKelas()) {
            return redirect()->intended('/wali-kelas/dashboard')
                ->with('success', 'Selamat datang, ' . $user->name . '!');
        } elseif ($user->isKepalaSekolah()) {
            return redirect()->intended('/kepala-sekolah/dashboard')
                ->with('success', 'Selamat datang, ' . $user->name . '!');
        }

        // Jika level tidak dikenali, logout dan redirect
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('error', 'Level user tidak valid.');
    }

    /**
     * Proses logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil logout.');
    }

    /**
     * Pastikan request login tidak di-rate limit.
     */
    protected function ensureIsNotRateLimited(Request $request): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => 'Terlalu banyak percobaan login. Silakan coba lagi dalam ' . $seconds . ' detik.',
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->string('email')) . '|' . $request->ip());
    }
}
