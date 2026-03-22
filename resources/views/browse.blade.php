@extends('layouts.app')

@section('content')
@php
    $browseHeadline = $browseHeadline ?? 'Discover Artists';
    $browseSubtitle = $browseSubtitle ?? 'Find creators by style, availability, and the kind of work they actually make.';
    $searchPlaceholder = $searchPlaceholder ?? 'Search artist name, username, or vibe...';
    $filterAvailability = $filterAvailability ?? 'Any availability';
    $filterOpen = $filterOpen ?? 'Open for commissions';
    $filterClosed = $filterClosed ?? 'Closed / no slots';
    $submitLabel = $submitLabel ?? 'Search';
    $resultsLabel = $resultsLabel ?? 'artists';
    $emptyMessage = $emptyMessage ?? 'No artists found matching your criteria.';
    $clearLabel = $clearLabel ?? 'Clear filters';

    $hasFilters = request()->query() && count(array_filter(request()->query(), fn ($value) => filled($value))) > 0;
    $activeQuery = trim((string) request('q'));
    $activeAvailability = trim((string) request('availability'));
    $activeTag = trim((string) request('tag'));
    $resultCount = $artists->count();
    $openCount = $artists->where(fn ($a) => (int) ($a->browse_slots_open ?? 0) > 0)->count();
@endphp

<div class="container py-8 md:py-12 browse-page">
    <section class="page-header browse-hero border-b-0 pb-6">
        <div class="browse-hero-copy">
            <x-badge variant="accent" size="sm" class="browse-kicker">
                Browse ateliers
            </x-badge>
            <h1 class="page-title browse-title">{!! $browseHeadline !!}</h1>
            <p class="page-subtitle browse-subtitle">{{ $browseSubtitle }}</p>
        </div>

        <div class="browse-stats-grid" aria-label="Browse stats">
            <div class="browse-stat-card">
                <div class="browse-stat-value">{{ number_format($totalArtists) }}</div>
                <div class="browse-stat-label">Total artists</div>
            </div>
            <div class="browse-stat-card">
                <div class="browse-stat-value">{{ number_format($openCount) }}</div>
                <div class="browse-stat-label">Open right now</div>
            </div>
            <div class="browse-stat-card">
                <div class="browse-stat-value">{{ number_format($resultCount) }}</div>
                <div class="browse-stat-label">Shown in results</div>
            </div>
        </div>

        <form action="{{ route('browse') }}" method="GET" class="browse-search-panel">
            <div class="browse-search-main">
                <x-input
                    name="q"
                    label="Search"
                    type="search"
                    :value="request('q')"
                    :placeholder="$searchPlaceholder"
                    class="browse-search-input"
                />
            </div>

            <div class="browse-filter-grid">
                <x-select
                    name="availability"
                    label="Availability"
                    :options="[
                        '' => $filterAvailability,
                        'open' => $filterOpen,
                        'closed' => $filterClosed,
                    ]"
                    :selected="request('availability')"
                />

                <x-select
                    name="tag"
                    label="Style"
                    :options="[
                        '' => 'Any style',
                        'furry' => 'Furry',
                        'nsfw' => 'NSFW',
                        'sfw' => 'SFW',
                        'character' => 'Character',
                        'design' => 'Design',
                        'anime' => 'Anime',
                        'queer' => 'Queer',
                    ]"
                    :selected="request('tag')"
                />
            </div>

            <div class="browse-search-actions">
                <x-button type="submit" variant="primary">
                    {{ $submitLabel }}
                </x-button>

                @if($hasFilters)
                    <x-button href="{{ route('browse') }}" variant="ghost" size="sm">
                        {{ $clearLabel }}
                    </x-button>
                @endif
            </div>
        </form>
    </section>

    <section class="browse-results-shell">
        <div class="browse-results-bar">
            <div>
                <div class="browse-results-eyebrow">Results</div>
                <div class="browse-results-title">{{ number_format($artists->total()) }} {{ $resultsLabel }}</div>
            </div>

            @if($hasFilters)
                <div class="browse-active-filters" aria-label="Active filters">
                    @if($activeQuery !== '')
                        <span class="browse-filter-chip">Search: {{ $activeQuery }}</span>
                    @endif
                    @if($activeAvailability !== '')
                        <span class="browse-filter-chip">{{ $activeAvailability === 'open' ? $filterOpen : $filterClosed }}</span>
                    @endif
                    @if($activeTag !== '')
                        <span class="browse-filter-chip">Style: {{ ucfirst($activeTag) }}</span>
                    @endif
                </div>
            @endif
        </div>

        @if($artists->isEmpty())
            <div class="browse-empty-wrap">
                <x-empty-state
                    :title="$emptyMessage"
                    description="Try a broader vibe, remove one filter, or reset the search and start from the full artist pool."
                    actionRoute="{{ route('browse') }}"
                    actionLabel="Clear search"
                    icon="⟳"
                />
            </div>
        @else
            <div class="browse-grid">
                @foreach($artists as $artist)
                    <x-artist-card
                        :artist="$artist"
                        variant="list"
                        :showActions="auth()->check()"
                        :showGallery="false"
                    />
                @endforeach
            </div>

            <div class="pagination">
                {{ $artists->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
