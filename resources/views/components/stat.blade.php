@props(['title', 'value', 'description' => null, 'icon' => null])

<div class="stat">
    @if ($icon)
        <div class="stat-figure text-primary">
            <x-icon :name="$icon" />
        </div>
    @endif
    <div class="stat-title">{{ $title }}</div>
    <div class="stat-value text-primary">{{ $value }}</div>
    @if ($description)
        <div class="stat-desc">{{ $description }}</div>
    @endif
</div>
