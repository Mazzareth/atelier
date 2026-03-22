{{--
/**
 * x-input.blade.php
 * ----------
 * @prop name: string
 * @prop label: string|null
 * @prop type: text|email|password|number|url|tel|search  (default: text)
 * @prop value: mixed
 * @prop placeholder: string
 * @prop error: string|null
 * @prop hint: string|null
 * @prop required: bool
 * @prop disabled: bool
 * @prop autocomplete: string
 */
--}}
@props([
    'name' => null,
    'label' => null,
    'type' => 'text',
    'value' => null,
    'placeholder' => '',
    'error' => null,
    'hint' => null,
    'required' => false,
    'disabled' => false,
    'autocomplete' => null,
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

    <input
        type="{{ $type }}"
        @if($fieldId) id="{{ $fieldId }}" @endif
        @if($name) name="{{ $name }}" @endif
        value="{{ $value ?? ($name ? old($name) : '') }}"
        placeholder="{{ $placeholder }}"
        @if($required) required @endif
        @if($disabled) disabled @endif
        @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
        class="form-input {{ $error ? 'error' : '' }}"
        {{ $attributes->except('id')->class(['border-color: var(--color-error)' => $error]) }}
    >

    @if($error)
        <span class="form-error">{{ $error }}</span>
    @elseif($hint)
        <span class="form-hint">{{ $hint }}</span>
    @endif
</div>
