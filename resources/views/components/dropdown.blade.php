@props([
    'name',
    'label' => null,
    'options' => [],
    'placeholder' => '-- Select --',
    'value' => '',
    'required' => false,
    'hideError' => false
])

@php
    $dropdownId = $attributes->get('id', $name);
    $hasError = $errors->has($name);
@endphp

<div class="mb-4">
    @if ($label)
        <label for="{{ $dropdownId }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            {{ $label }}
        </label>
    @endif

    <select
        name="{{ $name }}"
        id="{{ $dropdownId }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'bg-gray-50 border ' . ($hasError ? 'border-red-500' : 'border-gray-300') . ' text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500']) }}
    >
        <option value="">{{ $placeholder }}</option>
        @foreach ($options as $key => $option)
            <option value="{{ $key }}" {{ old($name, $value) == $key ? 'selected' : '' }}>
                {{ $option }}
            </option>
        @endforeach
    </select>
    @if (!$hideError)
    @error($name)
        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
    @enderror
    @endif
</div>
