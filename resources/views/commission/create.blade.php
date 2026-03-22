@extends('layouts.app')

@php
    $identity = $viewerIdentity ?? null;
    $identityLabel = $theme->getIdentityLabel($identity) ?? 'Guest';
    $commissionPrompt = $theme->identityAware('commission', $identity, 'Tell the artist what you want.');
    $themeDescription = $theme->get('description');
@endphp

@section('content')
<div class="commission-request-page">
    <div class="commission-request-shell">
        <x-badge variant="accent" size="md" class="commission-request-kicker">
            ● commission request
        </x-badge>

        <h1 class="commission-request-title serif">Request a piece from {{ $artist->name }}</h1>
        <p class="commission-request-intro mono">
            {{ $commissionPrompt }}
            Once you send it, this turns into a shared thread with chat, status changes, and follow-up questions.
            @if($theme->requiresIdentity() && $identity)
                You're entering as <strong>{{ $identityLabel }}</strong> under <strong>{{ $theme->all()['name'] ?? ucfirst($themeName) }}</strong>.
            @elseif($themeDescription)
                {{ $themeDescription }}
            @endif
        </p>

        <x-card padding="lg">
            <form method="POST" action="{{ route('commission.store', $artist->username) }}" enctype="multipart/form-data" class="commission-request-form">
                @csrf

                <div class="commission-request-grid">
                    <x-input
                        name="title"
                        label="Title"
                        :value="old('title')"
                        required
                        maxlength="160"
                        :error="$errors->first('title')"
                        placeholder="Portrait, badge set, reference sheet..."
                    />

                    <x-input
                        name="budget"
                        type="number"
                        label="Budget (optional)"
                        :value="old('budget')"
                        min="0"
                        step="0.01"
                        :error="$errors->first('budget')"
                        placeholder="150.00"
                    />
                </div>

                <x-textarea
                    name="details"
                    label="Project brief"
                    :value="old('details')"
                    rows="10"
                    maxlength="5000"
                    required
                    :error="$errors->first('details')"
                    :hint="$commissionPrompt"
                    placeholder="Character details, pose ideas, mood, deadline, intended use, and anything the artist should know."
                />

                <div class="form-group">
                    <label for="references" class="form-label">References (optional)</label>
                    <input
                        id="references"
                        name="references[]"
                        type="file"
                        accept="image/*"
                        multiple
                        class="form-input commission-request-file {{ $errors->has('references') || $errors->has('references.*') ? 'error' : '' }}"
                    >
                    @if ($errors->has('references') || $errors->has('references.*'))
                        <span class="form-error">{{ $errors->first('references') ?: $errors->first('references.*') }}</span>
                    @else
                        <span class="form-hint commission-request-file-note">Attach up to 6 images, 10MB each. They’ll be added to the request thread and the artist’s workspace.</span>
                    @endif
                </div>

                @if ($errors->any())
                    <div class="commission-request-errors mono">
                        <strong>Couldn’t send yet.</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="commission-request-meta">
                    <div class="commission-request-help mono">
                        Only logged-in users can request. The artist can accept, decline, ask for more info, and keep talking with you in the same thread.
                    </div>

                    <x-button type="submit" variant="primary">
                        Send Request
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
</div>
@endsection
