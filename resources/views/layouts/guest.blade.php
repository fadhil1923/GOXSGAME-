<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'GOXSGAME') }} — Masuk Portal Game</title>
        <meta name="description" content="GOXSGAME — Platform game edukasi interaktif untuk guru dan siswa Indonesia.">

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800;900&family=Space+Grotesk:wght@600;700;800;900&family=JetBrains+Mono:wght@700;800&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,700,1,0" rel="stylesheet">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            /* Base Animated Sliding GOXSGAME Watermark Background */
            body {
                background-color: #2563EB;
                background-image: 
                    url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='650' height='300'%3E%3Ctext x='25' y='120' font-family='Arial, sans-serif' font-size='90' font-weight='900' font-style='italic' fill='rgba(255,255,255,0.07)'%3EGOXSGAME%3C/text%3E%3C/svg%3E"),
                    url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='650' height='300'%3E%3Ctext x='25' y='270' font-family='Arial, sans-serif' font-size='90' font-weight='900' font-style='italic' fill='rgba(255,255,255,0.07)'%3EGOXSGAME%3C/text%3E%3C/svg%3E");
                background-size: 650px 300px, 650px 300px;
                background-position: 0 0, 0 0;
                animation: slideBg 14s linear infinite;
                transition: background-color 0.3s ease;
            }
            @keyframes slideBg {
                0%   { background-position: 0 0,     0 0; }
                100% { background-position: -650px 0, 650px 0; }
            }

            /* Dark Mode Overrides */
            html.dark body {
                background-color: #0B1120 !important;
                color: #F8FAFC !important;
                background-image: 
                    url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='650' height='300'%3E%3Ctext x='25' y='120' font-family='Arial, sans-serif' font-size='90' font-weight='900' font-style='italic' fill='rgba(255,255,255,0.03)'%3EGOXSGAME%3C/text%3E%3C/svg%3E"),
                    url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='650' height='300'%3E%3Ctext x='25' y='270' font-family='Arial, sans-serif' font-size='90' font-weight='900' font-style='italic' fill='rgba(255,255,255,0.03)'%3EGOXSGAME%3C/text%3E%3C/svg%3E") !important;
            }

            /* Neo-Brutalism Bouncy Box */
            .neo-box {
                border: 4px solid #000000;
                border-radius: 32px;
                box-shadow: 0px 10px 0px #000000;
                background: #ffffff;
                color: #000000;
                transition: all 0.2s ease;
            }

            html.dark .neo-box {
                background-color: #1E293B !important;
                color: #F8FAFC !important;
                border-color: #000000 !important;
            }

            html.dark .neo-box h2,
            html.dark .neo-box h3,
            html.dark .neo-box label {
                color: #F8FAFC !important;
            }

            html.dark .neo-box .text-black:not(.bg-\[\#FFE600\]):not(.bg-\[\#4ADE80\]):not(.bg-\[\#FB7185\]):not(.bg-\[\#06B6D4\]):not([class*="bg-"]):not(button) {
                color: #F8FAFC !important;
            }

            html.dark .text-stone-600,
            html.dark .text-stone-500 {
                color: #94A3B8 !important;
            }

            html.dark .text-stone-400 {
                color: #CBD5E1 !important;
            }

            html.dark .bg-\[\#FEF9EF\] {
                background-color: #0F172A !important;
                color: #F8FAFC !important;
            }

            .neo-btn {
                border: 3px solid #000000;
                border-radius: 999px;
                box-shadow: 0px 6px 0px #000000;
                font-weight: 900;
                transition: all 0.1s ease;
            }
            .neo-btn:not([class*="text-"]) { color: #000000; }
            .neo-btn.text-white, .neo-btn.text-white * { color: #FFFFFF !important; }
            .neo-btn.text-black, .neo-btn.text-black * { color: #000000 !important; }
            .neo-btn.bg-black { background-color: #000000 !important; color: #FFE600 !important; }
            .neo-btn.bg-black * { color: #FFE600 !important; }
            .neo-btn:hover {
                filter: brightness(1.05);
            }
            .neo-btn:active {
                transform: translateY(6px);
                box-shadow: 0px 0px 0px #000000;
            }

            .neo-input {
                border: 3px solid #000000;
                border-radius: 999px;
                box-shadow: 0px 5px 0px #000000;
                font-weight: 700;
                color: #000000;
                transition: all 0.1s ease;
            }
            .neo-input:focus {
                outline: none;
                border-color: #000000;
                box-shadow: 0px 3px 0px #000000;
                transform: translateY(2px);
            }

            .relative > .pointer-events-none {
                z-index: 10;
            }

            html.dark .neo-input {
                background-color: #0F172A !important;
                color: #F8FAFC !important;
                border-color: #000000 !important;
            }

            html.dark .neo-input::placeholder {
                color: #64748B !important;
            }

            @keyframes popIn {
                0% { transform: scale(0.85); opacity: 0; }
                60% { transform: scale(1.03); opacity: 1; }
                100% { transform: scale(1); opacity: 1; }
            }
            .pop-in {
                animation: popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
            }
        </style>

        <script>
            // Initialize theme immediately to prevent flashing
            (function() {
                const savedTheme = localStorage.getItem('goxs_theme');
                if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            })();

            function toggleGuestTheme() {
                const isDark = document.documentElement.classList.toggle('dark');
                localStorage.setItem('goxs_theme', isDark ? 'dark' : 'light');
                updateGuestThemeIcon();
            }

            function updateGuestThemeIcon() {
                const icon = document.getElementById('guest-theme-icon');
                if (!icon) return;
                const isDark = document.documentElement.classList.contains('dark');
                icon.textContent = isDark ? 'light_mode' : 'dark_mode';
            }

            document.addEventListener('DOMContentLoaded', updateGuestThemeIcon);
        </script>
    </head>
    <body class="font-body text-white antialiased selection:bg-[#FFE600] selection:text-black min-h-screen flex flex-col justify-center items-center p-4 sm:p-6 relative overflow-x-hidden">
        {{-- Floating Theme Toggle Button --}}
        <div class="fixed top-4 right-4 z-50">
            <button type="button" onclick="toggleGuestTheme()" class="p-2.5 rounded-2xl border-3 border-black bg-white dark:bg-slate-800 text-black dark:text-white hover:bg-[#FFE600] dark:hover:bg-[#FFE600] dark:hover:text-black shadow-[3px_3px_0px_#000] flex items-center justify-center transition active:translate-y-1" title="Ganti Mode Gelap / Terang">
                <span class="material-symbols-outlined text-xl" id="guest-theme-icon">dark_mode</span>
            </button>
        </div>

        {{ $slot }}
    </body>
</html>
