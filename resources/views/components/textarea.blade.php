{{--
/**
 * x-textarea.blade.php
 * @prop name: string
 * @prop label: string|null
 * @prop value: string
 * @prop placeholder: string
 * @prop rows: int (default: 6)
 * @prop maxlength: int|null
 * @prop error: string|null
 * @prop hint: string|null
 * @prop required: bool
 */
--}}
@props([
    'name' => null,
    'label' => null,
    'value' => null,
    'placeholder' => '',
    'rows' => 6,
    'maxlength' => null,
    'error' => null,
    'hint' => null,
    'required' => false,
])

@php
    $fieldId = $attributes->get('id') ?? $name;
@endphp

<div class="form-group">
    @if($label)
        <label for="{{ $fieldId }}" class="form-label">
            {{ $label }}
            @if($required)<span class="text-accent">*</span>@endif
        </label>
    @endif

    <textarea
        @if($fieldId) id="{{ $fieldId }}" @endif
        @if($name) name="{{ $name }}" @endif
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        @if($maxlength) maxlength="{{ $maxlength }}" @endif
        @if($required) required @endif
        class="form-input form-textarea {{ $error ? 'error' : '' }}"
        {{ $attributes->except('id')->class(['border-color: var(--color-error)' => $error]) }}
    >{{ $value ?? ($name ? old($name) : '') }}</textarea>

    @if($error)
        <span class="form-error">{{ $error }}</span>
    @elseif($hint)
        <span class="form-hint">{{ $hint }}</span>
    @endif
</div>
