<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    public function index(): View
    {
        $users = collect();

        try {
            $users = User::where('role', 'artist')
                ->whereNotNull('username')
                ->pluck('username');
        } catch (\Throwable $e) {
            report($e);
        }

        return view('welcome', [
            'users' => $users,
        ]);
    }
}
