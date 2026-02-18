<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'check.level' => \App\Http\Middleware\CheckLevel::class,
        ]);

        // Configure redirect for unauthenticated users
        $middleware->redirectGuestsTo('/login');

        // Configure redirect for authenticated users accessing guest-only pages
        $middleware->redirectUsersTo(function (Request $request) {
            $user = $request->user();
            if ($user) {
                if ($user->isAdmin()) return '/admin/dashboard';
                if ($user->isWaliKelas()) return '/wali-kelas/dashboard';
                if ($user->isKepalaSekolah()) return '/kepala-sekolah/dashboard';
            }
            return '/';
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle "Page Expired" (419) - CSRF token mismatch
        // This commonly happens when the session expires while on the login page
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            return redirect()->route('login')
                ->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
        });
    })->create();
