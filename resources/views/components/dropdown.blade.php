@props([
    'name',
    'label' => null,
    'options' => [],
    'placeholder' => '-- Select --',
    'value' => '',
    'required' => false,
    'hideError' => false,
    'multiselect' => false
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

    @if($multiselect)
        <div class="relative">
            <div
                x-data="{
                    open: false,
                    selected: @entangle($name).live,
                    options: @js($options),
                    toggle() {
                        this.open = !this.open;
                    },
                    isSelected(key) {
                        return Array.isArray(this.selected) && this.selected.includes(key);
                    },
                    select(key) {
                        if (!Array.isArray(this.selected)) {
                            this.selected = [];
                        }
                        if (this.isSelected(key)) {
                            this.selected = this.selected.filter(item => item != key);
                        } else {
                            this.selected.push(key);
                        }
                        $wire.set('{{ $name }}', this.selected);
                    },
                    getSelectedText() {
                        if (!Array.isArray(this.selected) || this.selected.length === 0) {
                            return '{{ $placeholder }}';
                        }
                        if (this.selected.length === 1) {
                            return this.options[this.selected[0]] || 'Unknown';
                        }
                        return this.selected.length + ' selected';
                    }
                }"
                class="relative"
            >
                <button
                    type="button"
                    @click="toggle()"
                    class="bg-gray-50 border {{ $hasError ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 text-left dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                >
                    <span x-text="getSelectedText()"></span>
                    <svg class="w-4 h-4 absolute right-2 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div
                    x-show="open"
                    @click.away="open = false"
                    x-transition
                    class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg dark:bg-gray-700 dark:border-gray-600 max-h-60 overflow-y-auto"
                >
                    @foreach ($options as $key => $option)
                        <label class="flex items-center px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer">
                            <input
                                type="checkbox"
                                :checked="isSelected('{{ $key }}')"
                                @change="select('{{ $key }}')"
                                class="mr-2 w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                            >
                            <span class="text-gray-900 dark:text-white text-sm">{{ $option }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
    @else
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
    @endif
    @if (!$hideError)
    @error($name)
        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
    @enderror
    @endif
</div>
