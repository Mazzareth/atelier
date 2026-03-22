@extends('layouts.app')

@section('content')
<div class="container min-h-screen flex items-center justify-center py-12">
    <x-card padding="lg" class="w-full max-w-md relative overflow-hidden">
        {{-- Decorative element --}}
        <div class="absolute -top-8 -left-8 text-6xl opacity-10 pointer-events-none select-none" aria-hidden="true">
            ✦
        </div>

        <div class="relative z-10">
            <x-badge variant="muted" size="sm" class="mb-6">
                <span class="inline-block w-2 h-2 rounded-full bg-current mr-2"></span>
                Join
            </x-badge>
            
            <h1 class="text-4xl font-bold mb-2">
                Join as a <span class="text-accent">{{ $accountType === 'artist' ? 'Artist' : 'Client' }}.</span>
            </h1>
            <p class="text-muted mb-8">
                @if($accountType === 'artist')
                    Create your artist account. You'll be able to set up your profile and start receiving commission requests.
                @else
                    Create a client account to browse artists and send commission requests.
                @endif
            </p>

            @if($errors->any())
                <div class="bg-red-500/10 border-l-4 border-red-500 p-4 mb-6 text-red-500 text-sm">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register', ['type' => $accountType]) }}" class="flex flex-col gap-6">
                @csrf
                
                <x-input 
                    name="username" 
                    label="Username" 
                    :value="old('username')" 
                    required 
                    autofocus 
                />

                <x-input 
                    type="password" 
                    name="password" 
                    label="Password" 
                    required 
                />

                <x-input 
                    type="password" 
                    name="password_confirmation" 
                    label="Confirm Password" 
                    required 
                />

                <x-button type="submit" variant="primary" class="w-full justify-center mt-2">
                    Create {{ $accountType === 'artist' ? 'artist' : 'client' }} account <span class="ml-2">→</span>
                </x-button>
            </form>

            <div class="mt-8 text-center text-sm font-mono text-muted uppercase tracking-wide">
                Already have an account?
                <a href="{{ route('login') }}" class="text-accent hover:underline ml-1">Log in</a>
            </div>

            <div class="mt-4 text-center text-xs font-mono text-muted uppercase tracking-wide opacity-70">
                <a href="{{ route('onboard') }}" class="hover:underline">
                    ← Wrong type? Choose again
                </a>
            </div>
        </div>
    </x-card>
</div>
@endsection