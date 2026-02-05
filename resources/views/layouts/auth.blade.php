<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Autentificare — respins.ro')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
    <!-- Background -->
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-slate-950 via-slate-950 to-black"></div>
        <div class="absolute -top-24 left-1/2 h-[520px] w-[720px] -translate-x-1/2 rounded-full bg-indigo-600/25 blur-3xl"></div>
        <div class="absolute top-[35%] -left-40 h-[520px] w-[520px] rounded-full bg-cyan-500/20 blur-3xl"></div>
        <div class="absolute bottom-[-160px] right-[-160px] h-[560px] w-[560px] rounded-full bg-fuchsia-500/15 blur-3xl"></div>
    </div>

    <main class="flex min-h-screen items-center justify-center px-4">
        @yield('content')
    </main>
</body>
</html>
