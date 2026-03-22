@extends('layouts.app')

@section('content')
<div class="container min-h-screen flex items-center justify-center py-12">
    <div class="w-full max-w-4xl relative">
        {{-- Decorative element --}}
        <div class="absolute -top-16 -left-10 text-8xl opacity-10 pointer-events-none select-none" aria-hidden="true">
            ✦
        </div>

        <div class="relative z-10 text-center mb-12">
            <x-badge variant="muted" size="sm" class="mb-6">
                <span class="inline-block w-2 h-2 rounded-full bg-current mr-2"></span>
                Start here
            </x-badge>
            
            <h1 class="text-4xl md:text-5xl font-bold mb-4">
                What brings you <span class="text-accent">here?</span>
            </h1>
            <p class="text-lg text-muted max-w-lg mx-auto">
                Choose your path. You can always add capabilities later.
            </p>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            {{-- Artist Card --}}
            <a href="{{ route('register', ['type' => 'artist']) }}" class="group block no-underline h-full">
                <x-card padding="lg" hoverable class="h-full relative overflow-hidden transition-all duration-250 group-hover:-translate-y-1">
                    {{-- Card graphic --}}
                    <div class="absolute top-0 right-0 w-32 h-32 pointer-events-none rounded-bl-[100px] opacity-20" style="background: radial-gradient(circle at top right, var(--color-accent), transparent 70%);"></div>

                    <div class="relative z-10">
                        <x-badge variant="accent" size="sm" class="mb-5 bg-accent/10 border-accent/40">
                            <span class="inline-block w-2 h-2 rounded-full bg-accent mr-2"></span>
                            Artist
                        </x-badge>

                        <h2 class="text-3xl font-bold mb-3">
                            I want to <span class="text-accent">sell my work</span>
                        </h2>

                        <p class="text-muted leading-relaxed mb-6">
                            Set up an artist profile, receive commission requests, manage your workspace, and get paid for your creative work.
                        </p>

                        <div class="flex flex-col gap-3">
                            <div class="flex items-center gap-3 text-sm text-muted">
                                <span class="text-accent text-lg leading-none">+</span> Customizable artist page
                            </div>
                            <div class="flex items-center gap-3 text-sm text-muted">
                                <span class="text-accent text-lg leading-none">+</span> Commission request inbox
                            </div>
                            <div class="flex items-center gap-3 text-sm text-muted">
                                <span class="text-accent text-lg leading-none">+</span> Workspace & file sharing
                            </div>
                            <div class="flex items-center gap-3 text-sm text-muted">
                                <span class="text-accent text-lg leading-none">+</span> Revenue tracking
                            </div>
                        </div>

                        <div class="mt-8 text-sm font-mono text-accent uppercase tracking-wide group-hover:underline">
                            Create artist account →
                        </div>
                    </div>
                </x-card>
            </a>

            {{-- Client Card --}}
            <a href="{{ route('register', ['type' => 'client']) }}" class="group block no-underline h-full">
                <x-card padding="lg" hoverable class="h-full relative overflow-hidden transition-all duration-250 group-hover:-translate-y-1">
                    {{-- Card graphic --}}
                    <div class="absolute top-0 right-0 w-32 h-32 pointer-events-none rounded-bl-[100px] opacity-20" style="background: radial-gradient(circle at top right, var(--color-accent), transparent 70%);"></div>

                    <div class="relative z-10">
                        <x-badge variant="accent" size="sm" class="mb-5 bg-accent/10 border-accent/40">
                            <span class="inline-block w-2 h-2 rounded-full bg-accent mr-2"></span>
                            Client
                        </x-badge>

                        <h2 class="text-3xl font-bold mb-3">
                            I want to <span class="text-accent">hire an artist</span>
                        </h2>

                        <p class="text-muted leading-relaxed mb-6">
                            Browse artists, follow your favorites, send commission requests, and manage your projects in one place.
                        </p>

                        <div class="flex flex-col gap-3">
                            <div class="flex items-center gap-3 text-sm text-muted">
                                <span class="text-accent text-lg leading-none">+</span> Browse artist profiles
                            </div>
                            <div class="flex items-center gap-3 text-sm text-muted">
                                <span class="text-accent text-lg leading-none">+</span> Follow favorite artists
                            </div>
                            <div class="flex items-center gap-3 text-sm text-muted">
                                <span class="text-accent text-lg leading-none">+</span> Commission requests
                            </div>
                            <div class="flex items-center gap-3 text-sm text-muted">
                                <span class="text-accent text-lg leading-none">+</span> Message artists
                            </div>
                        </div>

                        <div class="mt-8 text-sm font-mono text-accent uppercase tracking-wide group-hover:underline">
                            Create client account →
                        </div>
                    </div>
                </x-card>
            </a>
        </div>

        <div class="mt-12 text-center text-sm font-mono text-muted uppercase tracking-wide">
            Already have an account?
            <a href="{{ route('login') }}" class="text-accent hover:underline ml-2">Log in here</a>
        </div>
    </div>
</div>
@endsection