@extends('layouts.app')

@section('content')
<div style="min-height: 80vh; padding: 4rem 1.25rem;">
    <div style="max-width: 840px; margin: 0 auto;">
        <div class="pill mono" style="margin-bottom: 1rem; border-color: var(--accent-color); color: var(--accent-color); background: var(--accent-dim);">
            <div class="dot" style="background: var(--accent-color);"></div>
            ● commission request
        </div>
        <h1 class="serif" style="font-size: 3rem; margin-bottom: 0.75rem;">Request a piece from {{ $artist->name }}</h1>
        <p class="mono" style="color: var(--text-muted); line-height: 1.7; margin-bottom: 2rem;">Send the artist a clean brief. Once submitted, this becomes a shared request thread with chat, status updates, and follow-up questions.</p>

        <form method="POST" action="{{ route('commission.store', $artist->username) }}" enctype="multipart/form-data" style="background: var(--bg-panel); border: 1px solid var(--border-color); border-radius: 16px; padding: 2rem; display: flex; flex-direction: column; gap: 1.25rem; box-shadow: 0 20px 50px rgba(0,0,0,0.2);">
            @csrf
            <div>
                <label class="mono" style="display: block; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.5rem;">Title</label>
                <input name="title" value="{{ old('title') }}" required maxlength="160" style="width: 100%; background: var(--bg-color); color: var(--text-main); border: 1px solid var(--border-color); padding: 0.95rem 1rem; border-radius: 10px; outline: none;">
            </div>
            <div>
                <label class="mono" style="display: block; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.5rem;">Budget (optional)</label>
                <input name="budget" type="number" step="0.01" min="0" value="{{ old('budget') }}" style="width: 100%; background: var(--bg-color); color: var(--text-main); border: 1px solid var(--border-color); padding: 0.95rem 1rem; border-radius: 10px; outline: none;">
            </div>
            <div>
                <label class="mono" style="display: block; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.5rem;">What do you want?</label>
                <textarea name="details" required maxlength="5000" style="width: 100%; min-height: 240px; background: var(--bg-color); color: var(--text-main); border: 1px solid var(--border-color); padding: 1rem; border-radius: 10px; outline: none; resize: vertical;">{{ old('details') }}</textarea>
            </div>
            <div>
                <label class="mono" style="display: block; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.5rem;">References (optional)</label>
                <input name="references[]" type="file" accept="image/*" multiple style="width: 100%; background: var(--bg-color); color: var(--text-main); border: 1px solid var(--border-color); padding: 0.85rem 1rem; border-radius: 10px; outline: none;">
                <div class="mono" style="margin-top: 0.45rem; font-size: 0.68rem; color: var(--text-muted);">Attach up to 6 reference images.</div>
            </div>
            @if ($errors->any())
                <div class="mono" style="color: #ff7b7b; font-size: 0.8rem; line-height: 1.7;">
                    @foreach ($errors->all() as $error)
                        <div>• {{ $error }}</div>
                    @endforeach
                </div>
            @endif
            <div style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center; justify-content: space-between;">
                <div class="mono" style="font-size: 0.72rem; color: var(--text-muted);">Only logged-in users can request. The artist can accept, decline, ask for more info, and chat with you from the same thread.</div>
                <button type="submit" class="btn btn-primary">Send Request</button>
            </div>
        </form>
    </div>
</div>
@endsection
