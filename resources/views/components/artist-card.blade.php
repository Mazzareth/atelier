{{--
/**
 * x-artist-card.blade.php
 * Artist profile card for browse and listings.
 * Fully theme-aware via CSS tokens.
 *
 * @prop artist: \App\Models\User
 * @prop variant: list | grid | compact    (default: list)
 * @prop showActions: bool
 * @prop showGallery: bool
 */
--}}
@props([
    'artist',
    'variant' => 'list',
    'showActions' => true,
    'showGallery' => false,
])

@php
    $avatarModule = $artist->profileModules->firstWhere('type', 'avatar_info');
    $bioModule = $artist->profileModules->firstWhere('type', 'bio');
    $galleryModule = $artist->profileModules->firstWhere('type', 'gallery_feed');
    $slotsModule = $artist->profileModules->firstWhere('type', 'comm_slots');

    $avatar = $avatarModule?->settings['avatar'] ?? null;
    $bio = trim(preg_replace(
        '/\s+/',
        ' ',
        preg_replace('/[#*_`>\-\[\]\(\)!]+/', ' ', strip_tags($bioModule->settings['text'] ?? ''))
    ));
    $slotsOpen = (int) ($slotsModule->settings['slots_open'] ?? 0);
    $isOpen = $slotsOpen > 0;
    $galleryImages = collect($galleryModule->settings['images'] ?? [])->take(4)->toArray();
    $isFollowing = (bool) ($artist->browse_is_following
        ?? (auth()->check()
            ? \Illuminate\Support\Facades\DB::table('followers')
                ->where('user_id', $artist->id)
                ->where('follower_id', auth()->id())
                ->exists()
            : false));
    $bioPreview = $bio ? Str::limit($bio, 150) : 'No bio yet — open the atelier and see what kind of work they are building.';
@endphp

<article class="artist-card @if($variant === 'grid') artist-card-grid @endif" data-artist-card>
    <a href="{{ route('artist.profile', $artist->username) }}" class="artist-card-mainlink">
        <div class="artist-card-header">
            <x-avatar
                :src="$avatar"
                :alt="$artist->name"
                size="lg"
                class="shrink-0"
            />

            <div class="artist-card-info">
                <div class="artist-card-topline">
                    <div>
                        <div class="artist-card-name">{{ $artist->name }}</div>
                        <div class="artist-card-handle">/{{ $artist->username }}</div>
                    </div>

                    @if($isOpen)
                        <x-badge variant="accent" size="sm">
                            {{ $slotsOpen }} {{ $slotsOpen === 1 ? 'slot' : 'slots' }} open
                        </x-badge>
                    @else
                        <x-badge variant="muted" size="sm">Closed</x-badge>
                    @endif
                </div>

                <p class="artist-card-bio">{{ $bioPreview }}</p>
            </div>
        </div>
    </a>

    {{-- Gallery strip (optional) --}}
    @if($showGallery && count($galleryImages) > 0)
        <div class="gallery-grid gallery-grid-grid artist-card-gallery">
            @foreach($galleryImages as $img)
                <div class="gallery-item"
                    @if(is_array($img) && isset($img['url']))
                        style="background-image:url('{{ $img['url'] }}')"
                    @elseif(is_string($img))
                        style="background-image:url('{{ $img }}')"
                    @endif
                ></div>
            @endforeach
        </div>
    @endif

    {{-- Footer with metrics + actions --}}
    <div class="artist-card-footer">
        <div class="artist-card-metrics">
            <div class="artist-card-metric">
                <span class="artist-card-metric-value" data-follower-count>{{ number_format($artist->follower_count) }}</span>
                <span class="artist-card-metric-label">Followers</span>
            </div>
            <div class="artist-card-metric">
                <span class="artist-card-metric-value">{{ $isOpen ? $slotsOpen : '—' }}</span>
                <span class="artist-card-metric-label">Open slots</span>
            </div>
        </div>

        @if($showActions && auth()->check())
            <div class="artist-card-actions">
                <x-follow-button :artist="$artist" :following="$isFollowing" variant="ghost" />

                <x-button
                    href="{{ route('conversations.start', $artist->username) }}"
                    variant="ghost"
                    size="sm"
                >
                    Message
                </x-button>

                <x-button
                    href="{{ route('commission.create', $artist->username) }}"
                    variant="primary"
                    size="sm"
                    :disabled="! $isOpen"
                >
                    {{ $isOpen ? 'Request commission' : 'Queue closed' }}
                </x-button>
            </div>
        @endif
    </div>
</article>
