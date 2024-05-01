<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Smart VCard Generator' }}</title>
    @livewireStyles
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-memphis-bg text-black min-h-screen relative">
    <div class="fixed inset-0 pointer-events-none overflow-hidden">
        <div class="absolute top-20 left-10 w-24 h-24 bg-acid-lime border-4 border-black rounded-full animate-float opacity-80"></div>
        <div class="absolute top-40 right-20 w-0 h-0 border-l-[40px] border-l-transparent border-r-[40px] border-r-transparent border-b-[70px] border-b-acid-pink animate-float-slow opacity-70" style="border-bottom-color: var(--color-acid-pink);"></div>
        <div class="absolute bottom-32 left-1/4 w-16 h-16 bg-acid-yellow border-4 border-black rotate-45 animate-wobble opacity-80"></div>
        <div class="absolute top-1/3 right-1/3 w-12 h-12 bg-acid-cyan border-4 border-black rounded-lg animate-float-fast opacity-70"></div>
        <div class="absolute bottom-20 right-10 w-20 h-20 bg-acid-orange border-4 border-black rounded-full animate-bounce-slow opacity-60"></div>
        <div class="absolute top-60 left-1/3 w-8 h-8 bg-acid-purple border-3 border-black animate-spin-slow opacity-70"></div>

        <svg class="absolute top-1/4 left-20 w-16 h-16 animate-float opacity-60" viewBox="0 0 100 100">
            <path d="M10 50 Q 30 10, 50 50 T 90 50" fill="none" stroke="#000" stroke-width="6" stroke-linecap="round"/>
        </svg>
        <svg class="absolute bottom-40 right-1/4 w-20 h-20 animate-wobble opacity-50" viewBox="0 0 100 100">
            <circle cx="20" cy="20" r="8" fill="#ff00aa"/>
            <circle cx="50" cy="50" r="8" fill="#ccff00"/>
            <circle cx="80" cy="80" r="8" fill="#ffe600"/>
            <circle cx="80" cy="20" r="8" fill="#00ffcc"/>
            <circle cx="20" cy="80" r="8" fill="#aa00ff"/>
        </svg>
    </div>

    <div class="relative z-10">
        {{ $slot }}
    </div>

    @livewireScripts
</body>
</html>
