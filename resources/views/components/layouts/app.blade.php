<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'DPMPTSP' }} - Sistem Persuratan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-base-200">
    <div class="drawer lg:drawer-open">
        <input id="sidebar-toggle" type="checkbox" class="drawer-toggle" />

        {{-- Main Content --}}
        <div class="drawer-content flex flex-col">
            {{-- Top Navbar --}}
            <x-navbar />

            {{-- Page Content --}}
            <main class="flex-1 p-6">
                {{-- Flash Messages --}}
                <x-alert />

                {{ $slot }}
            </main>
        </div>

        {{-- Sidebar --}}
        <x-sidebar />
    </div>
</body>

</html>
