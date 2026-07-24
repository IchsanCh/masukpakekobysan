@props([
    'loadingText' => 'Loading...',
    'class' => '',
])

<button type="submit" :disabled="loading" {{ $attributes->merge(['class' => 'btn ' . $class]) }}
    x-bind:class="loading ? 'opacity-70 cursor-not-allowed scale-100 shadow-none pointer-events-none' : ''">
    <span x-show="!loading">{{ $slot }}</span>
    <span x-show="loading" class="flex items-center justify-center gap-2">
        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
            </circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
        </svg>
        {{ $loadingText }}
    </span>
</button>
