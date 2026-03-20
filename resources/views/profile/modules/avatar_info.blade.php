<div class="module-wrapper {{ $isEditMode ?? false ? 'draggable' : '' }}" data-id="{{ $module->id }}" data-type="avatar_info" data-settings="{{ json_encode($module->settings ?? []) }}">
    @if($isEditMode ?? false)
    <div class="module-edit-overlay" style="top: 60px;">
        <div class="mod-btn drag-handle" title="Drag">↕</div>
        <div class="mod-btn mod-btn-edit" title="Edit Profile">✏</div>
        <div class="mod-btn mod-btn-delete" title="Delete" style="color: #ff4d4d;">🗑</div>
    </div>
    @endif
    <!-- Avatar & Identity Module -->
    <div class="profile-identity-card" style="display: flex; gap: 2rem; align-items: flex-end; padding: 0 2rem 1rem; position: relative; flex-wrap: wrap;">
        <div style="width: 140px; height: 140px; background: var(--bg-color); border: 4px solid var(--bg-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; position: relative; z-index: 10; overflow: hidden; box-shadow: 0 18px 50px rgba(0,0,0,0.35);">
            @if(!empty($module->settings['avatar']))
                <img src="{{ $module->settings['avatar'] }}" style="width: 100%; height: 100%; object-fit: cover;">
            @else
                <div style="width: 100%; height: 100%; background: var(--accent-dim); display: flex; align-items: center; justify-content: center; color: var(--accent-color); font-size: 3rem; font-family: 'Georgia', serif;">
                    {{ strtoupper(substr($artist->username ?? 'A', 0, 1)) }}
                </div>
            @endif
        </div>
        
        <div style="padding-bottom: 1rem; flex: 1 1 320px; min-width: 0;">
            <div style="display: flex; gap: 0.6rem; flex-wrap: wrap; margin-bottom: 1rem;">
                <span class="mono profile-theme-chip" style="font-size: 0.65rem; color: var(--accent-color); text-transform: uppercase; border: 1px solid var(--accent-color); background: var(--accent-dim); padding: 0.35rem 0.6rem; border-radius: 999px;">Theme Reactive</span>
                <span class="mono" style="font-size: 0.65rem; color: var(--text-muted); text-transform: uppercase; border: 1px solid var(--border-color); padding: 0.35rem 0.6rem; border-radius: 999px;">Artist Page</span>
            </div>
            <h1 class="serif" style="font-size: 3rem; margin: 0; line-height: 1;">{{ $artist->name ?? 'Artist Name' }}</h1>
            <div style="display: flex; gap: 1rem; align-items: center; margin-top: 0.5rem; flex-wrap: wrap;">
                <div class="mono" style="color: var(--accent-color); font-size: 1rem;">
                    {{ '/' . ($artist->username ?? 'username') }}
                </div>
                <div class="mono" style="font-size: 0.9rem; opacity: 0.8; padding-left: 0.5rem; border-left: 1px solid var(--border-color);">
                    <span class="follower-count-value">{{ number_format($artist->follower_count ?? 0) }}</span> followers
                </div>
            </div>
            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; margin-top: 1.25rem;">
                <button type="button" class="btn btn-ghost profile-copy-link-btn" style="padding: 0.65rem 1rem; font-size: 0.78rem;">
                    Copy Profile Link
                </button>
                <a href="#sidebar-zone" class="btn btn-ghost" style="padding: 0.65rem 1rem; font-size: 0.78rem; text-decoration: none;">View Links</a>
            </div>
        </div>
        
        @if($module->settings['show_follow'] ?? true)
        <div data-role="follow-cta-wrap" style="margin-left: auto; padding-bottom: 1rem;">
            @php
                $isFollowing = auth()->check() ? \Illuminate\Support\Facades\DB::table('followers')->where('user_id', $artist->id)->where('follower_id', auth()->id())->exists() : false;
            @endphp
            <button class="btn {{ $isFollowing ? 'btn-secondary' : 'btn-primary' }} follow-btn" 
                    data-username="{{ $artist->username }}"
                    style="padding: 0.8rem 2rem;">
                {{ $isFollowing ? 'Unfollow' : 'Follow' }}
            </button>
        </div>
        @endif
    </div>

    @push('scripts')
    @once
    <script>
    document.addEventListener('click', async function(e) {
        if (e.target.closest('.profile-copy-link-btn')) {
            try {
                await navigator.clipboard.writeText(window.location.href);
                const btn = e.target.closest('.profile-copy-link-btn');
                const original = btn.textContent;
                btn.textContent = 'Copied!';
                setTimeout(() => btn.textContent = original, 1400);
            } catch (err) {
                console.error(err);
            }
        }

        if (e.target.closest('.quick-tip-btn')) {
            const btn = e.target.closest('.quick-tip-btn');
            const amount = btn.dataset.amount;
            btn.textContent = `$${amount} ready`; 
            setTimeout(() => btn.textContent = `$${amount}`, 1400);
        }

        if (e.target.closest('.follow-btn')) {
            const btn = e.target.closest('.follow-btn');
            const username = btn.dataset.username;
            
            try {
                const res = await fetch(`/follow/${username}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
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
                    btn.classList.remove('btn-primary');
                    btn.classList.add('btn-secondary');
                } else if (data.status === 'unfollowed') {
                    btn.textContent = 'Follow';
                    btn.classList.remove('btn-secondary');
                    btn.classList.add('btn-primary');
                }
                
                const countDisplay = document.querySelector('.follower-count-value');
                if (countDisplay) {
                    countDisplay.textContent = data.follower_count.toLocaleString();
                }
            } catch (err) {
                console.error(err);
            }
        }
    });
    </script>
    @endonce
    @endpush
</div>
