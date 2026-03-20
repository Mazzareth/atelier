<?php

namespace App\Http\Controllers;

class OnboardingController extends Controller
{
    /**
     * Show the onboarding page where users choose their account type.
     */
    public function index()
    {
        return view('auth.onboard');
    }
}
