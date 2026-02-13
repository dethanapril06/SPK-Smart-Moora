<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
     * Proses login
     */
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.'
        ]);

        // Coba login
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Redirect berdasarkan level
            if ($user->isAdmin()) {
                return redirect()->intended('/admin/dashboard')->with('success', 'Selamat datang, ' . $user->name . '!');
            } elseif ($user->isWaliKelas()) {
                return redirect()->intended('/wali-kelas/dashboard')->with('success', 'Selamat datang, ' . $user->name . '!');
            } elseif ($user->isKepalaSekolah()) {
                return redirect()->intended('/kepala-sekolah/dashboard')->with('success', 'Selamat datang, ' . $user->name . '!');
            }

            // Jika level tidak dikenali, logout dan redirect
            Auth::logout();
            return redirect()->route('login')->with('error', 'Level user tidak valid.');
        }

        // Jika login gagal
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    /**
     * Proses logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }
}
