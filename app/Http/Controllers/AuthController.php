<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Rate limiting: 5 attempts per minute per IP+username
        $throttleKey = Str::lower($request->username) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()
                ->withErrors(['username' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik."])
                ->withInput($request->only('username'));
        }

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($throttleKey, 60);
            return back()
                ->withErrors(['username' => 'Username atau password salah.'])
                ->withInput($request->only('username'));
        }

        if (!$user->status) {
            return back()->withErrors(['username' => 'Akun Anda telah dinonaktifkan.']);
        }

        // Clear rate limiter on success
        RateLimiter::clear($throttleKey);

        // Regenerate session to prevent fixation
        $request->session()->regenerate();

        Auth::login($user, $request->boolean('remember'));

        $user->update(['last_login' => now()]);

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Berhasil keluar dari sistem.');
    }
}