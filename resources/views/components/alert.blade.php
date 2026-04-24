{{-- Flash message alerts --}}
@if (session('success'))
    <div role="alert" class="alert alert-success mb-4">
        <x-icon name="check-circle" />
        <span>{{ session('success') }}</span>
    </div>
@endif

@if (session('error'))
    <div role="alert" class="alert alert-error mb-4">
        <x-icon name="x-circle" />
        <span>{{ session('error') }}</span>
    </div>
@endif

@if (session('warning'))
    <div role="alert" class="alert alert-warning mb-4">
        <x-icon name="alert-triangle" />
        <span>{{ session('warning') }}</span>
    </div>
@endif

@if (session('info'))
    <div role="alert" class="alert alert-info mb-4">
        <x-icon name="info" />
        <span>{{ session('info') }}</span>
    </div>
@endif
