<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Enums\UserRole;
use App\Notifications\WelcomeEmail;
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
     * Show the registration form.
     */
    public function showRegister(Request $request)
    {
        $type = $request->query('type', 'client');

        return view('auth.register', [
            'accountType' => $type,
        ]);
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
     * Create a new account.
     */
    public function register(Request $request)
    {
        $payload = $request->validate([
            'username' => ['required', 'string', 'min:3', 'max:32', 'alpha_dash', 'unique:users,username'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $type = $request->query('type', 'client');

        // Determine role based on account type
        $role = match($type) {
            'artist' => UserRole::Artist,
            default => UserRole::Commissioner,
        };

        // Active profile starts as 'commissioner' for both, artist can switch to 'artist' mode
        $activeProfile = 'commissioner';

        // Auto-generate email based on username and type
        $emailDomain = $type === 'artist' ? 'artist.local' : 'client.local';

        $user = User::create([
            'name' => $payload['username'],
            'username' => $payload['username'],
            'email' => sprintf('%s@%s', Str::lower($payload['username']), $emailDomain),
            'password' => $payload['password'],
            'role' => $role,
            'active_profile' => $activeProfile,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        // Send welcome email
        $user->notify(new WelcomeEmail());

        // Redirect artists to their profile setup, clients to dashboard
        if ($type === 'artist') {
            // Artists go to their new public profile to start setting up
            return redirect()->to('/' . $user->username);
        }

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
