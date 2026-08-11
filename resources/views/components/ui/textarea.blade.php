@props([
    'name' => '',
    'label' => null,
    'error' => null,
    'hint' => null,
    'required' => false,
    'disabled' => false,
    'rows' => 4,
])

@php
    $hasError = $error || ($errors->has($name) ?? false);
    $errorMessage = $error ?? ($errors->first($name) ?? null);

    $inputClasses = 'w-full px-4 py-2.5 border rounded-base bg-white text-sand-900 placeholder-sand-400 transition-colors duration-200 focus:outline-none resize-none';
    $inputClasses .= $hasError
        ? ' border-danger-500 focus:border-danger-500 focus:ring-2 focus:ring-danger-500/20'
        : ' border-sand-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20';
@endphp

<div {{ $attributes->only('class')->merge(['class' => 'w-full']) }}>
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-sand-700 mb-1.5">
            {{ $label }}
            @if($required)
                <span class="text-danger-500">*</span>
            @endif
        </label>
    @endif

    <textarea
        name="{{ $name }}"
        id="{{ $name }}"
        rows="{{ $rows }}"
        {{ $attributes->except('class')->merge(['class' => $inputClasses]) }}
        @if($required) required @endif
        @if($disabled) disabled @endif
        @if($hasError) aria-invalid="true" aria-describedby="{{ $name }}-error" @endif
    >{{ $slot }}</textarea>

    @if($hasError)
        <p id="{{ $name }}-error" class="mt-1.5 text-sm text-danger-500">{{ $errorMessage }}</p>
    @elseif($hint)
        <p class="mt-1.5 text-sm text-sand-500">{{ $hint }}</p>
    @endif
</div>
