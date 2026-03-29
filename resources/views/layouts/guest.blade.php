<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="min-h-screen font-sans antialiased text-slate-800 bg-slate-50 selection:bg-violet-200 selection:text-violet-950">
    <div class="auth-bg pointer-events-none fixed inset-0 -z-10 overflow-hidden">
        <div
            class="absolute -top-32 -right-24 h-96 w-96 rounded-full bg-violet-400/25 blur-3xl">
        </div>
        <div class="absolute top-1/2 -left-32 h-80 w-80 -translate-y-1/2 rounded-full bg-cyan-400/20 blur-3xl"></div>
        <div class="absolute bottom-0 right-1/4 h-64 w-64 rounded-full bg-fuchsia-400/15 blur-3xl"></div>
    </div>

    @yield('content')
</body>

</html>
