@props([
    'type' => 'text', // can be 'text', 'textarea', 'file', etc.
    'name',
    'label' => null,
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'readonly' => false,
    'disabled' => false,
    'icon' => null,
])

@php
    $inputId = $attributes->get('id', $name);
    $hasError = $errors->has($name);
    $baseClasses = 'block w-full text-sm rounded-lg border ';
    $theme = 'bg-gray-50 border-gray-300 text-gray-900 focus:ring-blue-500 focus:border-blue-500';
    $darkTheme = 'dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500';
    $errorClass = $hasError ? 'border-red-500' : '';
@endphp

<div class="mb-4">
    @if ($label)
        <label for="{{ $inputId }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            {{ $label }}
        </label>
    @endif

    <div class="relative">
        {{-- Icon (optional) --}}
        @if ($icon)
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <i class="{{ $icon }} text-gray-500 dark:text-gray-400"></i>
            </div>
        @endif

        {{-- Textarea --}}
        @if ($type === 'textarea')
            <textarea
                name="{{ $name }}"
                id="{{ $inputId }}"
                placeholder="{{ $placeholder }}"
                {{ $required ? 'required' : '' }}
                {{ $readonly ? 'readonly' : '' }}
                {{ $disabled ? 'disabled' : '' }}
                {{ $attributes->merge([
                    'class' => ($icon ? 'pl-10 ' : '') .
                    "$baseClasses $theme $darkTheme $errorClass p-2.5 resize-none"
                ]) }}
            >{{ old($name, $value) }}</textarea>

        {{-- File input --}}
        @elseif ($type === 'file')
            <input
                type="file"
                name="{{ $name }}"
                id="{{ $inputId }}"
                {{ $required ? 'required' : '' }}
                {{ $readonly ? 'readonly' : '' }}
                {{ $disabled ? 'disabled' : '' }}
                {{ $attributes->merge([
                    'class' => "$baseClasses $theme $darkTheme $errorClass p-2.5"
                ]) }}
            />

        {{-- Standard input --}}
        @else
            <input
                type="{{ $type }}"
                name="{{ $name }}"
                id="{{ $inputId }}"
                value="{{ old($name, $value) }}"
                placeholder="{{ $placeholder }}"
                {{ $required ? 'required' : '' }}
                {{ $readonly ? 'readonly' : '' }}
                {{ $disabled ? 'disabled' : '' }}
                {{ $attributes->merge([
                    'class' => ($icon ? 'pl-10 ' : '') .
                    "$baseClasses $theme $darkTheme $errorClass p-2.5"
                ]) }}
            />
        @endif
    </div>

    {{-- Validation error --}}
    @error($name)
        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
    @enderror
</div>
