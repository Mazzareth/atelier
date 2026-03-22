{{--
/**
 * x-gallery-grid.blade.php
 * @prop images: array   [['url' => ..., 'alt' => ...], ...]
 * @prop layout: grid | featured | masonry   (default: grid)
 * @prop limit: int     Max images to show (default: 9)
 */
--}}
@props([
    'images' => [],
    'layout' => 'grid',
    'limit' => 9,
])

@php
    $displayImages = collect($images)->take($limit)->toArray();
@endphp

<div class="gallery-grid gallery-grid-{{ $layout }}">
    @forelse($displayImages as $img)
        @php
            $url = is_array($img) ? ($img['url'] ?? null) : (is_string($img) ? $img : null);
            $alt = is_array($img) ? ($img['alt'] ?? '') : '';
        @endphp
        <div
            class="gallery-item {{ $url ? 'gallery-item-has-image' : '' }}"
            @if($url)
                style="--gallery-image: url('{{ $url }}')"
            @endif
            @if($alt !== '') title="{{ $alt }}" @endif
        >
            @if($url)
                <img src="{{ $url }}" alt="{{ $alt }}" loading="lazy" class="gallery-item-img">
            @endif
        </div>
    @empty
        @for($i = 0; $i < min(3, $limit); $i++)
            <div class="gallery-item gallery-item-empty">
                <span class="gallery-item-empty-label">No image</span>
            </div>
        @endfor
    @endforelse
</div>
