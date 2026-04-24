@props(['title' => null, 'compact' => false])

<div {{ $attributes->merge(['class' => 'card bg-base-100 shadow-sm']) }}>
    <div class="card-body {{ $compact ? 'p-4' : '' }}">
        @if ($title)
            <h2 class="card-title text-lg">{{ $title }}</h2>
        @endif
        {{ $slot }}
    </div>
</div>
