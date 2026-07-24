{{-- Flash message alerts --}}
@if (session('success'))
    <div role="alert" class="alert alert-success mb-4" x-data="{ show: true }" x-show="show" x-transition>
        <x-icon name="check-circle" />
        <span>{{ session('success') }}</span>
        <button @click="show = false" class="btn btn-ghost btn-xs btn-circle ml-auto">✕</button>
    </div>
@endif

@if (session('error'))
    <div role="alert" class="alert alert-error mb-4" x-data="{ show: true }" x-show="show" x-transition>
        <x-icon name="x-circle" />
        <span>{{ session('error') }}</span>
        <button @click="show = false" class="btn btn-ghost btn-xs btn-circle ml-auto">✕</button>
    </div>
@endif

@if (session('warning'))
    <div role="alert" class="alert alert-warning mb-4" x-data="{ show: true }" x-show="show" x-transiti on>
        <x-icon name="alert-triangle" />
        <span>{{ session('warning') }}</span>
        <button @click="show = false" class="btn btn-ghost btn-xs btn-circle ml-auto">✕</button>
    </div>
@endif

@if (session('info'))
    <div role="alert" class="alert alert-info mb-4" x-data="{ show: true }" x-show="show" x-transition>
        <x-icon name="info" />
        <span>{{ session('info') }}</span>
        <button @click="show = false" class="btn btn-ghost btn-xs btn-circle ml-auto">✕</button>
    </div>
@endif
