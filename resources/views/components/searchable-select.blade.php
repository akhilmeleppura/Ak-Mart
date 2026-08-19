@props([
    'name',
    'id'          => null,
    'placeholder' => 'Search...',
    'multiple'    => false,
    'required'    => false,
    'disabled'    => false,
    'ajaxUrl'     => null,
    'minLength'   => 2,
    'options'     => [],        // Static options: [['id' => 1, 'text' => 'Label'], ...]
    'selected'    => null,      // Selected value(s) — int|string|array
    'allowClear'  => true,
    'class'       => '',
    'dropdownParent' => null,   // Useful in modals: '#modal-id'
    'templateResult' => null,   // JS function name for custom option template
])

@php
    $inputId    = $id ?? 'select-' . $name . '-' . Str::random(6);
    $isMultiple = $multiple || is_array($selected);
    $selectedArr = is_array($selected) ? $selected : ($selected !== null ? [$selected] : []);
@endphp

<select
    name="{{ $name }}{{ $isMultiple ? '[]' : '' }}"
    id="{{ $inputId }}"
    class="form-select ak-searchable-select {{ $class }}"
    {{ $isMultiple ? 'multiple' : '' }}
    {{ $required ? 'required' : '' }}
    {{ $disabled ? 'disabled' : '' }}
    data-searchable-select="true"
    data-ajax-url="{{ $ajaxUrl }}"
    data-min-length="{{ $minLength }}"
    data-placeholder="{{ $placeholder }}"
    data-allow-clear="{{ $allowClear ? 'true' : 'false' }}"
    data-dropdown-parent="{{ $dropdownParent }}"
    data-template-result="{{ $templateResult }}"
    style="width: 100%;"
>
    {{-- Blank placeholder option (required for allowClear) --}}
    @if(!$isMultiple && $allowClear)
        <option value=""></option>
    @endif

    {{-- Static options --}}
    @foreach ($options as $option)
        @php $val = is_array($option) ? ($option['id'] ?? $option[0]) : $option; @endphp
        @php $lbl = is_array($option) ? ($option['text'] ?? $option[1] ?? $option['id']) : $option; @endphp
        <option value="{{ $val }}" {{ in_array((string)$val, array_map('strval', $selectedArr)) ? 'selected' : '' }}>
            {{ $lbl }}
        </option>
    @endforeach

    {{-- Slot for custom options --}}
    {{ $slot ?? '' }}
</select>
