@extends('layouts.app')

@php
    $isOwner = auth()->check() && auth()->id() === $artist->id;
    $isEditMode = $isOwner && auth()->user()->isActingAsArtist();
@endphp

@section('content')
    @php
        $pageLayout = $artist->page_layout ?? 'classic';
    @endphp

    <div class="profile-page profile-page--{{ $pageLayout }}" style="min-height: 100vh; padding-bottom: 8rem; position: relative; width: 100%; overflow-x: hidden;">
        
        <!-- HEADER ZONE -->
        <div id="header-zone" class="profile-header-zone {{ $isEditMode ? 'sortable-zone edit-zone' : '' }}" data-zone="header">
            @foreach($modules->get('header', []) as $mod)
                @include('profile.modules.' . $mod->type, ['module' => $mod])
            @endforeach
        </div>

        <div id="profile-layout-grid" class="profile-layout-grid">
            @include('profile.partials.classic-layout', ['modules' => $modules, 'isEditMode' => $isEditMode, 'pageLayout' => $pageLayout])
        </div>
    </div>

    <!-- Modals -->
    @if($isEditMode)
        @include('profile.partials.edit-drawer')
        @include('profile.partials.edit-modal')
    @endif

    @include('profile.partials.pricing-sheet-modal')

@endsection

@push('scripts')
    <style>
        .profile-header-zone {
            width: 100%;
            display: flex;
            flex-direction: column;
            padding: 0 4rem;
            max-width: 100%;
            margin: 0 auto;
            box-sizing: border-box;
        }

        .profile-layout-grid {
            width: 100%;
            max-width: 100%;
            margin: 3rem auto 0;
            padding: 0 4rem;
            display: grid;
            grid-template-columns: minmax(0, 2fr) minmax(280px, 1fr);
            gap: 5rem;
            box-sizing: border-box;
            align-items: start;
        }

        .profile-main-column,
        .profile-sidebar-column {
            display: flex;
            flex-direction: column;
            gap: 2rem;
            min-height: 100px;
        }

        .profile-module-card {
            position: relative;
            box-shadow: 0 18px 40px rgba(0,0,0,0.16);
        }

        .profile-module-card--spotlight::before {
            content: '';
            position: absolute;
            inset: -1px;
            border-radius: inherit;
            background: linear-gradient(135deg, color-mix(in srgb, var(--accent-color) 25%, transparent), transparent 45%, color-mix(in srgb, var(--accent-color) 12%, transparent));
            pointer-events: none;
            opacity: 0.8;
        }

        .profile-link-card:hover {
            border-color: var(--accent-color) !important;
            transform: translateY(-2px);
        }

        .gallery-feed-preview {
            display: grid;
            gap: 0.85rem;
        }

        .gallery-feed-preview--grid,
        .gallery-feed-preview--masonry {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .gallery-feed-preview--featured {
            grid-template-columns: 1.4fr 1fr 1fr;
        }

        .gallery-preview-card {
            min-height: 110px;
            border-radius: 14px;
            border: 1px solid color-mix(in srgb, var(--accent-color) 18%, var(--border-color));
            background:
                linear-gradient(135deg, color-mix(in srgb, var(--accent-color) 20%, transparent), transparent 50%),
                linear-gradient(180deg, color-mix(in srgb, var(--bg-color) 75%, transparent), color-mix(in srgb, var(--bg-panel) 95%, transparent));
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.04);
        }

        .gallery-feed-preview--masonry .gallery-preview-card:nth-child(2),
        .gallery-feed-preview--masonry .gallery-preview-card:nth-child(5) {
            min-height: 160px;
        }

        .gallery-feed-preview--featured .gallery-preview-card:first-child {
            min-height: 240px;
            grid-row: span 2;
        }

        .commission-tracker-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .theme-swatch-btn {
            border: 1px solid var(--border-color);
            background: color-mix(in srgb, var(--bg-color) 85%, transparent);
            color: var(--text-main);
            padding: 0.7rem 0.5rem;
            border-radius: 10px;
            font-size: 0.68rem;
            text-transform: uppercase;
            cursor: pointer;
        }

        .theme-swatch-btn:hover {
            border-color: var(--accent-color);
            color: var(--accent-color);
            background: var(--accent-dim);
        }

        .profile-main-column-wrap,
        .profile-sidebar-column-wrap {
            min-width: 0;
        }

        .profile-page--fixed_left .profile-layout-grid {
            grid-template-columns: minmax(260px, 0.95fr) minmax(0, 1.85fr);
        }

        .profile-page--fixed_left .profile-main-column-wrap {
            order: 2;
        }

        .profile-page--fixed_left .profile-sidebar-column-wrap {
            order: 1;
        }

        .profile-sidebar-column-wrap--sticky {
            position: sticky;
            top: 2rem;
            align-self: start;
            max-height: calc(100vh - 4rem);
            overflow-y: auto;
            overflow-x: hidden;
            padding-right: 0.35rem;
        }

        .profile-page--editorial .profile-layout-grid {
            grid-template-columns: minmax(0, 1.55fr) minmax(250px, 0.85fr);
            gap: 3rem;
            max-width: 1500px;
        }

        .profile-page--editorial .profile-main-column {
            gap: 2.5rem;
        }

        .profile-page--magazine .profile-layout-grid {
            grid-template-columns: minmax(0, 1.15fr) minmax(320px, 1fr);
            gap: 2.5rem;
            max-width: 1550px;
        }

        .profile-page--magazine .profile-sidebar-column {
            gap: 1.25rem;
        }

        .profile-page--stacked .profile-layout-grid {
            grid-template-columns: minmax(0, 1fr);
            gap: 2rem;
            max-width: 1100px;
        }

        .profile-page--stacked .profile-main-column-wrap {
            order: 1;
        }

        .profile-page--stacked .profile-sidebar-column-wrap {
            order: 2;
        }

        .profile-page--stacked .profile-sidebar-column-wrap--sticky {
            position: static;
            max-height: none;
            overflow: visible;
            padding-right: 0;
        }

        @media (max-width: 1200px) {
            .commission-tracker-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 980px) {
            .profile-header-zone,
            .profile-layout-grid {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }

            .profile-layout-grid {
                grid-template-columns: 1fr !important;
                gap: 1.5rem !important;
                max-width: 100% !important;
            }

            .profile-main-column-wrap,
            .profile-sidebar-column-wrap {
                order: initial !important;
            }

            .profile-sidebar-column-wrap--sticky {
                position: static;
                top: auto;
                max-height: none;
                overflow: visible;
                padding-right: 0;
            }

            .commission-tracker-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    @include('profile.partials.edit-scripts')
@endpush
