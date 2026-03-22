@extends('layouts.app')

@section('content')
<div class="container min-h-screen flex items-center justify-center py-12">
    <x-card padding="lg" class="w-full max-w-md relative overflow-hidden">
        {{-- Decorative element --}}
        <div class="absolute -top-8 -left-8 text-6xl opacity-10 pointer-events-none select-none" aria-hidden="true">
            🔑
        </div>
        
        <div class="relative z-10">
            <x-badge variant="muted" size="sm" class="mb-6">
                <span class="inline-block w-2 h-2 rounded-full bg-current mr-2"></span>
                Access
            </x-badge>
            
            <h1 class="text-4xl font-bold mb-2">Welcome back.</h1>
            <p class="text-muted mb-8">Log in with your username or email to reach your Atelier or personal feed.</p>

            @if($errors->any())
                <div class="bg-red-500/10 border-l-4 border-red-500 p-4 mb-6 text-red-500 text-sm">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-6">
                @csrf
                
                <x-input 
                    name="login" 
                    label="Username or Email" 
                    :value="old('login')" 
                    required 
                    autofocus 
                />

                <x-input 
                    type="password" 
                    name="password" 
                    label="Password" 
                    required 
                />

                <x-button type="submit" variant="primary" class="w-full justify-center mt-2">
                    Log in <span class="ml-2">→</span>
                </x-button>
            </form>

            <div class="mt-8 text-center text-sm font-mono text-muted uppercase tracking-wide">
                New here?
                <a href="{{ route('onboard') }}" class="text-accent hover:underline ml-1">Create an account</a>
            </div>
        </div>
    </x-card>
</div>
@endsection
