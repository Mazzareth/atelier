<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Show the commissioner signup form.
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle authentication.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $loginField = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([$loginField => $credentials['login'], 'password' => $credentials['password']])) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Smart redirection based on role
            if ($user->isAdmin()) {
                return redirect()->intended('/admin/dashboard');
            }
            
            if ($user->isActingAsArtist()) {
                return redirect()->intended('/atelier/dashboard');
            }

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'login' => 'The provided credentials do not match our records.',
        ])->onlyInput('login');
    }

    /**
     * Create a new commissioner account.
     */
    public function register(Request $request)
    {
        $payload = $request->validate([
            'username' => ['required', 'string', 'min:3', 'max:32', 'alpha_dash', 'unique:users,username'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $payload['username'],
            'username' => $payload['username'],
            'email' => sprintf('%s@commissioner.local', Str::lower($payload['username'])),
            'password' => $payload['password'],
            'role' => 'commissioner',
            'active_profile' => 'commissioner',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    /**
     * Log out the user.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
