{{--
/**
 * x-select.blade.php
 * @prop name: string
 * @prop label: string|null
 * @prop options: array  [value => label] or [['value' => x, 'label' => y]]
 * @prop selected: mixed
 * @prop placeholder: string
 * @prop error: string|null
 * @prop required: bool
 */
--}}
@props([
    'name' => null,
    'label' => null,
    'options' => [],
    'selected' => null,
    'placeholder' => '',
    'error' => null,
    'required' => false,
])

@php
    $fieldId = $attributes->get('id') ?? $name;
    $selectedValue = $selected ?? ($name ? old($name) : null);
@endphp

<div class="form-group">
    @if($label)
        <label for="{{ $fieldId }}" class="form-label">
            {{ $label }}
            @if($required)<span class="text-accent">*</span>@endif
        </label>
    @endif

    <select
        @if($fieldId) id="{{ $fieldId }}" @endif
        @if($name) name="{{ $name }}" @endif
        class="form-input form-select {{ $error ? 'error' : '' }}"
        @if($required) required @endif
        {{ $attributes->except('id')->class(['border-color: var(--color-error)' => $error]) }}
    >
        @if($placeholder)
            <option value="" disabled @selected(!$selectedValue)>{{ $placeholder }}</option>
        @endif

        @foreach($options as $key => $val)
            @if(is_array($val))
                <optgroup label="{{ $val['label'] ?? $key }}">
                    @foreach($val['options'] ?? [] as $k => $v)
                        <option value="{{ $k }}" @selected((string)$selectedValue === (string)$k)>{{ $v }}</option>
                    @endforeach
                </optgroup>
            @else
                <option value="{{ $key }}" @selected((string)$selectedValue === (string)$key)>{{ $val }}</option>
            @endif
        @endforeach
    </select>

    @if($error)
        <span class="form-error">{{ $error }}</span>
    @endif
</div>
