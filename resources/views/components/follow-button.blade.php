{{--
/**
 * x-follow-button.blade.php
 * AJAX follow/unfollow toggle. No page reload.
 * @prop artist: \App\Models\User
 * @prop following: bool
 * @prop variant: ghost | primary    (default: ghost)
 */
--}}
@props([
    'artist',
    'following' => false,
    'variant' => 'ghost',
])

@php
    $label = $following ? 'Unfollow' : 'Follow';
@endphp

<button
    type="button"
    class="btn btn-{{ $variant }} btn-sm"
    data-follow-btn
    data-username="{{ $artist->username }}"
    data-original-text="{{ $label }}"
    {{ $attributes }}
>
    {{ $label }}
</button>

@once
    @push('scripts')
        <script>
            document.addEventListener('click', async function (e) {
                const btn = e.target.closest('[data-follow-btn]');
                if (!btn) return;

                const username = btn.dataset.username;
                btn.disabled = true;

                try {
                    const res = await fetch(`/follow/${username}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                            'Accept': 'application/json'
                        }
                    });

                    if (res.status === 401) {
                        window.location.href = '/login';
                        return;
                    }

                    const data = await res.json();

                    if (data.status === 'followed') {
                        btn.textContent = 'Unfollow';
                    } else {
                        btn.textContent = btn.dataset.originalText || 'Follow';
                    }

                    const card = btn.closest('[data-artist-card]');
                    const countEl = card?.querySelector('[data-follower-count]');
                    if (countEl && typeof data.follower_count !== 'undefined') {
                        countEl.textContent = Number(data.follower_count).toLocaleString();
                    }
                } catch (err) {
                    console.error(err);
                } finally {
                    btn.disabled = false;
                }
            });
        </script>
    @endpush
@endonce
