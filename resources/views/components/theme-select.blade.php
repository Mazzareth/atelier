{{--
/**
 * x-theme-select.blade.php
 * The theme picker dropdown.
 * @prop compact: bool   (smaller variant for guest nav)
 */
--}}
@props([
    'compact' => false,
])

@php
    $themeGroups = [
        'Themes' => [
            'default' => 'Atelier',
            'rubber' => 'Latex',
            'femboy' => 'Femboy',
            'dominant' => 'Dom',
        ],
    ];
@endphp

<div class="theme-select-wrapper">
    <select
        class="theme-select {{ $compact ? 'text-xs theme-select-compact' : '' }}"
        data-theme-selector
    >
        @foreach($themeGroups as $group => $themes)
            <optgroup label="{{ $group }}">
                @foreach($themes as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </optgroup>
        @endforeach
    </select>
</div>
