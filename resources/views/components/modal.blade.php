@props(['id', 'title' => null])

<dialog id="{{ $id }}" {{ $attributes->merge(['class' => 'modal']) }}>
    <div class="modal-box">
        @if ($title)
            <h3 class="text-lg font-bold">{{ $title }}</h3>
        @endif

        <button onclick="{{ $id }}.close()"
            class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>

        <div class="py-4">
            {{ $slot }}
        </div>

        @if (isset($actions))
            <div class="modal-action">
                {{ $actions }}
            </div>
        @endif
    </div>
    <form method="dialog" class="modal-backdrop"><button>close</button></form>
</dialog>
