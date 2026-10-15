<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>@yield('title', 'GOXSGAME') — Platform Game Edukasi</title>
    <meta name="description" content="GOXSGAME — Platform game edukasi interaktif untuk guru dan siswa Indonesia.">
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800;900&family=Space+Grotesk:wght@600;700;800;900&family=JetBrains+Mono:wght@700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,700,1,0" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Base Background Animasi Teks GOXSGAME Berjalan */
        body {
            background-color: #2563EB;
            background-image: 
                url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='650' height='300'%3E%3Ctext x='25' y='120' font-family='Arial, sans-serif' font-size='90' font-weight='900' font-style='italic' fill='rgba(255,255,255,0.06)'%3EGOXSGAME%3C/text%3E%3C/svg%3E"),
                url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='650' height='300'%3E%3Ctext x='25' y='270' font-family='Arial, sans-serif' font-size='90' font-weight='900' font-style='italic' fill='rgba(255,255,255,0.06)'%3EGOXSGAME%3C/text%3E%3C/svg%3E");
            background-size: 650px 300px, 650px 300px;
            background-position: 0 0, 0 0;
            animation: slideBg 12s linear infinite;
            transition: background-color 0.3s ease;
        }
        @keyframes slideBg {
            0%   { background-position: 0 0,     0 0; }
            100% { background-position: -650px 0, 650px 0; }
        }

        /* ================= DARK MODE COMPREHENSIVE OVERRIDES ================= */
        html.dark body {
            background-color: #0B1120 !important;
            color: #F8FAFC !important;
            background-image: 
                url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='650' height='300'%3E%3Ctext x='25' y='120' font-family='Arial, sans-serif' font-size='90' font-weight='900' font-style='italic' fill='rgba(255,255,255,0.03)'%3EGOXSGAME%3C/text%3E%3C/svg%3E"),
                url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='650' height='300'%3E%3Ctext x='25' y='270' font-family='Arial, sans-serif' font-size='90' font-weight='900' font-style='italic' fill='rgba(255,255,255,0.03)'%3EGOXSGAME%3C/text%3E%3C/svg%3E") !important;
        }

        /* Standard uncolored Neo-Box surfaces in Dark Mode */
        html.dark .neo-box:not([class*="bg-[#FFE600]"]):not([class*="bg-[#4ADE80]"]):not([class*="bg-[#06B6D4]"]):not([class*="bg-[#FB7185]"]):not([class*="bg-[#2563EB]"]):not([class*="bg-[#A855F7]"]):not([class*="bg-[#FB923C]"]):not([class*="bg-yellow-"]):not([class*="bg-emerald-"]):not([class*="bg-green-"]):not([class*="bg-cyan-"]):not([class*="bg-rose-"]):not([class*="bg-blue-"]):not([class*="bg-purple-"]):not([class*="bg-orange-"]) {
            background-color: #1E293B !important;
            color: #F8FAFC !important;
            border-color: #000000 !important;
        }

        /* Colored Neo-Boxes keep their vibrant identity & proper contrast */
        /* Bright yellow: yellow background & black text */
        html.dark .neo-box:not([class*="peer-checked"])[class~="bg-[#FFE600]"],
        html.dark .neo-box:not([class*="peer-checked"]).bg-\[\#FFE600\],
        html.dark .neo-box:not([class*="peer-checked"])[class~="bg-yellow-400"] {
            background-color: #FFE600 !important;
            color: #000000 !important;
        }
        html.dark .neo-box:not([class*="peer-checked"])[class~="bg-[#FFE600]"] *:not(.bg-black):not([class*="bg-black"]):not([class*="bg-stone-900"]),
        html.dark .neo-box:not([class*="peer-checked"]).bg-\[\#FFE600\] *:not(.bg-black):not([class*="bg-black"]):not([class*="bg-stone-900"]) {
            color: #000000 !important;
        }

        /* Bright green: lime background & black text */
        html.dark .neo-box:not([class*="peer-checked"])[class~="bg-[#4ADE80]"],
        html.dark .neo-box:not([class*="peer-checked"]).bg-\[\#4ADE80\],
        html.dark .neo-box:not([class*="peer-checked"])[class~="bg-emerald-400"],
        html.dark .neo-box:not([class*="peer-checked"])[class~="bg-green-400"] {
            background-color: #4ADE80 !important;
            color: #000000 !important;
        }
        html.dark .neo-box:not([class*="peer-checked"])[class~="bg-[#4ADE80]"] *:not(.bg-black):not([class*="bg-black"]),
        html.dark .neo-box:not([class*="peer-checked"]).bg-\[\#4ADE80\] *:not(.bg-black):not([class*="bg-black"]) {
            color: #000000 !important;
        }

        /* Bright cyan: cyan background & black text */
        html.dark .neo-box:not([class*="peer-checked"])[class~="bg-[#06B6D4]"],
        html.dark .neo-box:not([class*="peer-checked"]).bg-\[\#06B6D4\],
        html.dark .neo-box:not([class*="peer-checked"])[class~="bg-cyan-400"] {
            background-color: #06B6D4 !important;
            color: #000000 !important;
        }
        html.dark .neo-box:not([class*="peer-checked"])[class~="bg-[#06B6D4]"] *,
        html.dark .neo-box:not([class*="peer-checked"]).bg-\[\#06B6D4\] * {
            color: #000000 !important;
        }

        /* Bright rose/pink: rose background & black text */
        html.dark .neo-box:not([class*="peer-checked"])[class~="bg-[#FB7185]"],
        html.dark .neo-box:not([class*="peer-checked"]).bg-\[\#FB7185\],
        html.dark .neo-box:not([class*="peer-checked"])[class~="bg-rose-400"] {
            background-color: #FB7185 !important;
            color: #000000 !important;
        }
        html.dark .neo-box:not([class*="peer-checked"])[class~="bg-[#FB7185]"] *,
        html.dark .neo-box:not([class*="peer-checked"]).bg-\[\#FB7185\] * {
            color: #000000 !important;
        }

        /* Royal blue: blue background & white text */
        html.dark .neo-box:not([class*="peer-checked"])[class~="bg-[#2563EB]"],
        html.dark .neo-box:not([class*="peer-checked"]).bg-\[\#2563EB\] {
            background-color: #2563EB !important;
            color: #FFFFFF !important;
        }
        html.dark .neo-box:not([class*="peer-checked"])[class~="bg-[#2563EB]"] *,
        html.dark .neo-box:not([class*="peer-checked"]).bg-\[\#2563EB\] * {
            color: #FFFFFF !important;
        }

        /* Purple: purple background & white text */
        html.dark .neo-box:not([class*="peer-checked"])[class~="bg-[#A855F7]"],
        html.dark .neo-box:not([class*="peer-checked"]).bg-\[\#A855F7\] {
            background-color: #A855F7 !important;
            color: #FFFFFF !important;
        }
        html.dark .neo-box:not([class*="peer-checked"])[class~="bg-[#A855F7]"] *,
        html.dark .neo-box:not([class*="peer-checked"]).bg-\[\#A855F7\] * {
            color: #FFFFFF !important;
        }

        /* Preserve black badge elements inside bright cards */
        html.dark .neo-box .bg-black,
        html.dark .neo-box .bg-black * {
            background-color: #000000 !important;
            color: #FFE600 !important;
        }

        /* Sidebar & Header */
        html.dark #main-sidebar {
            background-color: #1E293B !important;
            color: #F8FAFC !important;
        }
        html.dark header {
            background-color: #1E293B !important;
            color: #F8FAFC !important;
        }

        /* General Typography on dark backgrounds */
        html.dark header h2,
        html.dark header h3,
        html.dark header p,
        html.dark header span:not(.neo-tag):not([class*="bg-"]):not([class*="text-"]):not(.material-symbols-outlined),
        html.dark .neo-box:not([class*="bg-[#FFE600]"]):not([class*="bg-[#4ADE80]"]):not([class*="bg-[#06B6D4]"]):not([class*="bg-[#FB7185]"]) h1,
        html.dark .neo-box:not([class*="bg-[#FFE600]"]):not([class*="bg-[#4ADE80]"]):not([class*="bg-[#06B6D4]"]):not([class*="bg-[#FB7185]"]) h2,
        html.dark .neo-box:not([class*="bg-[#FFE600]"]):not([class*="bg-[#4ADE80]"]):not([class*="bg-[#06B6D4]"]):not([class*="bg-[#FB7185]"]) h3,
        html.dark .neo-box:not([class*="bg-[#FFE600]"]):not([class*="bg-[#4ADE80]"]):not([class*="bg-[#06B6D4]"]):not([class*="bg-[#FB7185]"]) h4,
        html.dark .neo-box:not([class*="bg-[#FFE600]"]):not([class*="bg-[#4ADE80]"]):not([class*="bg-[#06B6D4]"]):not([class*="bg-[#FB7185]"]) h5,
        html.dark .neo-box:not([class*="bg-[#FFE600]"]):not([class*="bg-[#4ADE80]"]):not([class*="bg-[#06B6D4]"]):not([class*="bg-[#FB7185]"]) h6,
        html.dark .neo-box:not([class*="bg-[#FFE600]"]):not([class*="bg-[#4ADE80]"]):not([class*="bg-[#06B6D4]"]):not([class*="bg-[#FB7185]"]) label,
        html.dark .neo-box:not([class*="bg-[#FFE600]"]):not([class*="bg-[#4ADE80]"]):not([class*="bg-[#06B6D4]"]):not([class*="bg-[#FB7185]"]) th,
        html.dark .neo-box:not([class*="bg-[#FFE600]"]):not([class*="bg-[#4ADE80]"]):not([class*="bg-[#06B6D4]"]):not([class*="bg-[#FB7185]"]) td,
        html.dark .neo-box:not([class*="bg-[#FFE600]"]):not([class*="bg-[#4ADE80]"]):not([class*="bg-[#06B6D4]"]):not([class*="bg-[#FB7185]"]) strong:not(.text-black) {
            color: #F8FAFC;
        }

        /* Generic text-black inside dark surfaces (except inside bright buttons/tags/light previews) */
        html.dark .neo-box:not([class*="bg-[#FFE600]"]):not([class*="bg-[#4ADE80]"]):not([class*="bg-[#06B6D4]"]):not([class*="bg-[#FB7185]"]) .text-black:not([class*="bg-[#FFE600]"]):not([class*="bg-[#4ADE80]"]):not([class*="bg-[#06B6D4]"]):not([class*="bg-[#FB7185]"]):not([class*="bg-[#FCD34D]"]):not([class*="bg-yellow-"]):not([class*="bg-emerald-"]):not([class*="bg-cyan-"]):not([class*="bg-rose-"]):not(.neo-tag):not(.neo-btn):not(#btn-theme-light *):not(.theme-card-light *):not(#feedback *) {
            color: #F8FAFC !important;
        }

        /* Subtitles & Muted text in Dark Mode */
        html.dark .text-stone-500,
        html.dark .text-stone-600,
        html.dark .text-stone-700,
        html.dark .text-stone-800,
        html.dark .text-gray-500,
        html.dark .text-gray-600,
        html.dark .text-gray-700,
        html.dark .text-slate-500,
        html.dark .text-slate-600 {
            color: #94A3B8 !important;
        }
        html.dark .text-stone-400,
        html.dark .text-gray-400,
        html.dark .text-slate-400 {
            color: #CBD5E1 !important;
        }

        /* Inner container backgrounds */
        html.dark .bg-stone-50,
        html.dark .bg-stone-100,
        html.dark .bg-stone-200 {
            background-color: #334155 !important;
            color: #F8FAFC !important;
        }
        html.dark .bg-[#FEF9EF] {
            background-color: #0F172A !important;
            color: #F8FAFC !important;
        }
        html.dark tr.hover\:bg-\[\#FEF9EF\]:hover {
            background-color: #334155 !important;
        }
        html.dark tr:hover {
            background-color: rgba(51, 65, 85, 0.4) !important;
        }

        /* Dividers and borders */
        html.dark .divide-black\/10,
        html.dark .border-black\/10,
        html.dark .border-black\/20 {
            border-color: rgba(255, 255, 255, 0.15) !important;
        }

        /* Inputs, selects, textareas */
        html.dark input.neo-input,
        html.dark select.neo-input,
        html.dark textarea.neo-input,
        html.dark .blooket-input,
        html.dark input[type="text"],
        html.dark input[type="search"],
        html.dark input[type="password"],
        html.dark input[type="email"],
        html.dark input[type="number"],
        html.dark select {
            background-color: #0F172A !important;
            color: #F8FAFC !important;
            border-color: #000000 !important;
        }
        html.dark input::placeholder,
        html.dark textarea::placeholder,
        html.dark .blooket-input::placeholder {
            color: #64748B !important;
        }

        /* Buttons & Tags with bright backgrounds MUST KEEP pitch black text */
        html.dark .bg-\[\#FFE600\],
        html.dark .bg-\[\#4ADE80\],
        html.dark .bg-\[\#FCD34D\],
        html.dark .bg-\[\#FB7185\],
        html.dark .bg-\[\#06B6D4\],
        html.dark .bg-yellow-300,
        html.dark .bg-yellow-400,
        html.dark .bg-emerald-400,
        html.dark .bg-green-400,
        html.dark .bg-cyan-400,
        html.dark .bg-rose-400,
        html.dark [class*="bg-[#FFE600]"],
        html.dark [class*="bg-[#4ADE80]"],
        html.dark [class*="bg-[#06B6D4]"],
        html.dark [class*="bg-[#FB7185]"] {
            color: #000000 !important;
        }
        html.dark .bg-\[\#FFE600\] *:not(.bg-black):not([class*="bg-black"]):not([class*="bg-stone-900"]),
        html.dark .bg-\[\#4ADE80\] *:not(.bg-black):not([class*="bg-black"]):not([class*="bg-stone-900"]),
        html.dark .bg-\[\#06B6D4\] *:not(.bg-black):not([class*="bg-black"]):not([class*="bg-stone-900"]),
        html.dark .bg-\[\#FB7185\] *:not(.bg-black):not([class*="bg-black"]):not([class*="bg-stone-900"]) {
            color: #000000 !important;
        }
        html.dark .bg-black.text-\[\#FFE600\],
        html.dark .bg-black [class*="text-[#FFE600]"],
        html.dark [class*="bg-black"] [class*="text-[#FFE600]"] {
            color: #FFE600 !important;
        }

        /* White buttons in dark mode become sleek dark slate with white text */
        html.dark .neo-btn.bg-white:not(#btn-theme-light):not(.theme-card-light),
        html.dark a.bg-white:not([class*="bg-[#"]):not(#btn-theme-light):not(.theme-card-light),
        html.dark button.bg-white:not(#btn-theme-light):not(.theme-card-light) {
            background-color: #334155 !important;
            color: #F8FAFC !important;
        }
        html.dark .neo-btn.bg-white:hover:not(#btn-theme-light):not(.theme-card-light),
        html.dark a.bg-white:not([class*="bg-[#"]):hover:not(#btn-theme-light):not(.theme-card-light),
        html.dark button.bg-white:hover:not(#btn-theme-light):not(.theme-card-light) {
            background-color: #475569 !important;
        }

        /* Sidebar link active & inactive states */
        html.dark #main-sidebar a:not(.bg-\[\#FFE600\]):not(.bg-\[\#2563EB\]):not(.bg-\[\#4ADE80\]):not(.bg-\[\#A855F7\]):not(.bg-\[\#06B6D4\]):not(.bg-\[\#FB7185\]) {
            background-color: #334155 !important;
            color: #F8FAFC !important;
        }
        html.dark #main-sidebar a:not(.bg-\[\#FFE600\]):not(.bg-\[\#2563EB\]):not(.bg-\[\#4ADE80\]):not(.bg-\[\#A855F7\]):not(.bg-\[\#06B6D4\]):not(.bg-\[\#FB7185\]):hover {
            background-color: #FFE600 !important;
            color: #000000 !important;
        }
        html.dark #main-sidebar a.bg-\[\#FFE600\] {
            background-color: #FFE600 !important;
            color: #000000 !important;
        }
        html.dark #main-sidebar a.bg-\[\#FFE600\] * {
            color: #000000 !important;
        }

        /* Feedback dialog in student game */
        html.dark #feedback {
            color: #000000 !important;
        }
        html.dark #feedback p,
        html.dark #feedback div,
        html.dark #feedback span {
            color: #000000 !important;
        }

        /* Laravel Pagination Dark Mode */
        html.dark nav[role="navigation"] {
            color: #F8FAFC !important;
        }
        html.dark nav[role="navigation"] span,
        html.dark nav[role="navigation"] a {
            background-color: #1E293B !important;
            border-color: #334155 !important;
            color: #F8FAFC !important;
        }
        html.dark nav[role="navigation"] a:hover {
            background-color: #FFE600 !important;
            color: #000000 !important;
        }
        html.dark nav[role="navigation"] span[aria-current="page"] span {
            background-color: #2563EB !important;
            color: #FFFFFF !important;
        }

        /* Universal Neo Back Button */
        .neo-back-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 0.9rem;
            border-radius: 0.75rem;
            border: 2px solid #000000;
            background-color: #FFFFFF;
            color: #000000;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 900;
            font-size: 0.8rem;
            text-transform: uppercase;
            box-shadow: 2px 2px 0px #000000;
            transition: all 0.15s ease;
            width: fit-content;
            text-decoration: none;
        }
        .neo-back-btn:hover {
            background-color: #FFE600;
            color: #000000;
            transform: translate(-1px, -1px);
            box-shadow: 3px 3px 0px #000000;
        }
        .neo-back-btn:active {
            transform: translate(2px, 2px);
            box-shadow: 0px 0px 0px #000000;
        }
        html.dark .neo-back-btn {
            background-color: #1E293B !important;
            color: #F8FAFC !important;
            border-color: #000000 !important;
        }
        html.dark .neo-back-btn:hover {
            background-color: #FFE600 !important;
            color: #000000 !important;
        }
        
        /* Bouncy Game UI Base */
        .neo-box { border: 4px solid #000000; border-radius: 32px; box-shadow: 0px 8px 0px #000000; background: #fff; color: #000000; transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        .neo-box:hover { transform: translateY(-4px); box-shadow: 0px 12px 0px #000000; }
        
        .neo-btn { border: 4px solid #000000; border-radius: 999px; box-shadow: 0px 6px 0px #000000; font-weight: 900; transition: all 0.1s; }
        .neo-btn:not([class*="text-"]) { color: #000000; }
        .neo-btn.text-white, .neo-btn.text-white * { color: #FFFFFF !important; }
        .neo-btn.text-black, .neo-btn.text-black * { color: #000000 !important; }
        .neo-btn.bg-black { background-color: #000000 !important; color: #FFE600 !important; }
        .neo-btn.bg-black * { color: #FFE600 !important; }
        .neo-btn.bg-black.text-\[\#4ADE80\], .neo-btn.bg-black.text-\[\#4ADE80\] * { color: #4ADE80 !important; }
        .neo-btn:active { transform: translateY(6px); box-shadow: 0px 0px 0px #000000; }
        
        .neo-tag { border: 3px solid #000000; box-shadow: 0px 3px 0px #000000; font-weight: 900; border-radius: 999px; }
        .neo-tag:not([class*="text-"]) { color: #000000; }
        .neo-tag.text-white, .neo-tag.text-white * { color: #FFFFFF !important; }
        .neo-tag.text-black, .neo-tag.text-black * { color: #000000 !important; }
        
        .neo-input { border: 4px solid #000000; border-radius: 999px; box-shadow: 0px 6px 0px #000000; font-weight: 700; padding: 12px 24px; color: #000000; transition: all 0.1s; }
        .neo-input:focus { outline: none; box-shadow: 0px 3px 0px #000000; transform: translateY(3px); }
    </style>
    <script>
        // Init theme immediately to prevent flashing
        (function() {
            const savedTheme = localStorage.getItem('goxs_theme');
            if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
    @stack('styles')
</head>
<body class="font-body text-white antialiased selection:bg-[#2563EB] selection:text-white relative overflow-x-hidden min-h-screen flex flex-col lg:flex-row">

{{-- ======================= MOBILE SIDEBAR BACKDROP ======================= --}}
@auth
<div id="mobile-sidebar-backdrop" onclick="toggleMobileSidebar()" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 hidden lg:hidden transition-opacity duration-300"></div>

{{-- ======================= SIDEBAR (DESKTOP & MOBILE DRAWER) ======================= --}}
<aside id="main-sidebar" class="fixed top-0 bottom-0 left-0 w-72 max-w-[85vw] bg-white text-black border-r-4 lg:border-4 border-black lg:rounded-[32px] shadow-[0px_8px_0px_#000] z-50 flex flex-col justify-between py-6 px-4 overflow-y-auto transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0 lg:left-6 lg:top-6 lg:bottom-6">
    <div class="flex flex-col gap-4">
        {{-- Brand & Mobile Close --}}
        <div class="p-2 flex flex-col gap-2">
            <div class="flex items-center justify-between">
                <a href="{{ route('dashboard') }}" class="bg-white border-2 border-black p-2 rounded-xl shadow-[3px_3px_0px_#000000] flex items-center justify-center font-display font-black text-xl hover:bg-[#FFE600] transition">
                    GOXSGAME
                </a>
                <div class="flex items-center gap-2">
                    <span class="neo-tag text-[10px] bg-[#{{ auth()->user()->role === 'admin' ? 'FB7185' : (auth()->user()->role === 'teacher' ? '4ADE80' : '2563EB') }}] text-black px-2 py-0.5 rounded uppercase tracking-wider font-mono">
                        {{ strtoupper(auth()->user()->role ?? 'user') }}
                    </span>
                    {{-- Tombol Tutup Mobile --}}
                    <button type="button" onclick="toggleMobileSidebar()" class="lg:hidden p-1 rounded-lg border-2 border-black bg-white hover:bg-[#FB7185] transition flex items-center justify-center text-black">
                        <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                </div>
            </div>
            <div class="flex items-center justify-between px-1">
                <span class="neo-tag text-[10px] bg-[#06B6D4] text-black px-1.5 py-0.5 rounded font-mono">VERSE 2.4</span>
            </div>
        </div>

        {{-- Navigation per role --}}
        <nav class="flex flex-col gap-2">
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl {{ request()->routeIs('dashboard') ? 'bg-[#FFE600] text-black border-3 border-black shadow-[3px_3px_0px_#000000]' : 'bg-white text-black border-2 border-black shadow-[2px_2px_0px_#000000] hover:bg-[#FFE600]' }} font-display font-black tracking-wide transition">
                    <span class="material-symbols-outlined text-xl">dashboard</span>
                    <span class="text-sm uppercase">Dashboard</span>
                </a>
                <a href="{{ route('manage.games.index') }}"
                   class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl {{ request()->routeIs('manage.games.*') ? 'bg-[#2563EB] text-white border-3 border-black shadow-[3px_3px_0px_#000000]' : 'bg-white text-black border-2 border-black shadow-[2px_2px_0px_#000000] hover:bg-[#FFE600]' }} font-display font-black tracking-wide transition">
                    <span class="material-symbols-outlined text-xl">sports_esports</span>
                    <span class="text-sm uppercase">Kelola Game</span>
                </a>
                <a href="{{ route('manage.users.index') }}"
                   class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl {{ request()->routeIs('manage.users.*') ? 'bg-[#4ADE80] text-black border-3 border-black shadow-[3px_3px_0px_#000000]' : 'bg-white text-black border-2 border-black shadow-[2px_2px_0px_#000000] hover:bg-[#FFE600]' }} font-display font-black tracking-wide transition">
                    <span class="material-symbols-outlined text-xl">manage_accounts</span>
                    <span class="text-sm uppercase">Kelola User</span>
                </a>
                <a href="{{ route('manage.subjects.index') }}"
                   class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl {{ request()->routeIs('manage.subjects.*') ? 'bg-[#A855F7] text-white border-3 border-black shadow-[3px_3px_0px_#000000]' : 'bg-white text-black border-2 border-black shadow-[2px_2px_0px_#000000] hover:bg-[#FFE600]' }} font-display font-black tracking-wide transition">
                    <span class="material-symbols-outlined text-xl">menu_book</span>
                    <span class="text-sm uppercase">Mata Pelajaran</span>
                </a>
                <a href="{{ route('manage.classes.index') }}"
                   class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl {{ request()->routeIs('manage.classes.*') ? 'bg-[#06B6D4] text-black border-3 border-black shadow-[3px_3px_0px_#000000]' : 'bg-white text-black border-2 border-black shadow-[2px_2px_0px_#000000] hover:bg-[#FFE600]' }} font-display font-black tracking-wide transition">
                    <span class="material-symbols-outlined text-xl">school</span>
                    <span class="text-sm uppercase">Master Kelas</span>
                </a>
                <a href="{{ route('manage.settings.index') }}"
                   class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl {{ request()->routeIs('manage.settings.*') ? 'bg-[#FB7185] text-black border-3 border-black shadow-[3px_3px_0px_#000000]' : 'bg-white text-black border-2 border-black shadow-[2px_2px_0px_#000000] hover:bg-[#FFE600]' }} font-display font-black tracking-wide transition">
                    <span class="material-symbols-outlined text-xl">settings</span>
                    <span class="text-sm uppercase">Pengaturan</span>
                </a>
                <a href="{{ route('manage.feedback.index') }}"
                   class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl {{ request()->routeIs('manage.feedback.*') ? 'bg-[#FFE600] text-black border-3 border-black shadow-[3px_3px_0px_#000000]' : 'bg-white text-black border-2 border-black shadow-[2px_2px_0px_#000000] hover:bg-[#FFE600]' }} font-display font-black tracking-wide transition">
                    <span class="material-symbols-outlined text-xl">mark_chat_unread</span>
                    <span class="text-sm uppercase">Pesan Pengembangan</span>
                    @php $unreadFeedbackCount = \App\Models\DevelopmentMessage::where('status', 'pending')->count(); @endphp
                    @if($unreadFeedbackCount > 0)
                        <span class="ml-auto bg-[#FB7185] text-black text-[10px] font-mono font-black px-1.5 py-0.5 rounded-full border border-black shadow-[1px_1px_0px_#000]">{{ $unreadFeedbackCount }}</span>
                    @endif
                </a>
            @elseif(auth()->user()->role === 'teacher')
                <a href="{{ route('manage.games.index') }}"
                   class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl {{ request()->routeIs('manage.games.*') ? 'bg-[#FFE600] text-black border-3 border-black shadow-[3px_3px_0px_#000000]' : 'bg-white text-black border-2 border-black shadow-[2px_2px_0px_#000000] hover:bg-[#FFE600]' }} font-display font-black tracking-wide transition">
                    <span class="material-symbols-outlined text-xl">sports_esports</span>
                    <span class="text-sm uppercase">Bank Game & Soal</span>
                </a>
                <a href="{{ route('teacher.rooms.index') }}"
                   class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl {{ request()->routeIs('teacher.rooms.index') ? 'bg-[#2563EB] text-white border-3 border-black shadow-[3px_3px_0px_#000000]' : 'bg-white text-black border-2 border-black shadow-[2px_2px_0px_#000000] hover:bg-[#4ADE80]' }} font-display font-black tracking-wide transition">
                    <span class="material-symbols-outlined text-xl">meeting_room</span>
                    <span class="text-sm uppercase">Room Saya</span>
                </a>
                <a href="{{ route('teacher.rooms.create') }}"
                   class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl {{ request()->routeIs('teacher.rooms.create') ? 'bg-[#4ADE80] text-black border-3 border-black shadow-[3px_3px_0px_#000000]' : 'bg-white text-black border-2 border-black shadow-[2px_2px_0px_#000000] hover:bg-[#FFE600]' }} font-display font-black tracking-wide transition">
                    <span class="material-symbols-outlined text-xl">add_circle</span>
                    <span class="text-sm uppercase">Buka Room Game</span>
                </a>
                <a href="{{ route('feedback.create') }}"
                   class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl {{ request()->routeIs('feedback.*') ? 'bg-[#A855F7] text-white border-3 border-black shadow-[3px_3px_0px_#000000]' : 'bg-white text-black border-2 border-black shadow-[2px_2px_0px_#000000] hover:bg-[#FFE600]' }} font-display font-black tracking-wide transition">
                    <span class="material-symbols-outlined text-xl">rate_review</span>
                    <span class="text-sm uppercase">Pesan Pengembangan</span>
                </a>
            @elseif(auth()->user()->role === 'student')
                <a href="{{ route('student.dashboard') }}"
                   class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl {{ request()->routeIs('student.dashboard') ? 'bg-[#FFE600] text-black border-3 border-black shadow-[3px_3px_0px_#000000]' : 'bg-white text-black border-2 border-black shadow-[2px_2px_0px_#000000] hover:bg-[#FFE600]' }} font-display font-black tracking-wide transition">
                    <span class="material-symbols-outlined text-xl">dashboard</span>
                    <span class="text-sm uppercase">Dashboard</span>
                </a>
                <a href="{{ route('student.join') }}"
                   class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl {{ request()->routeIs('student.join') ? 'bg-[#2563EB] text-white border-3 border-black shadow-[3px_3px_0px_#000000]' : 'bg-white text-black border-2 border-black shadow-[2px_2px_0px_#000000] hover:bg-[#FFE600]' }} font-display font-black tracking-wide transition">
                    <span class="material-symbols-outlined text-xl">dialpad</span>
                    <span class="text-sm uppercase">Masukkan PIN</span>
                </a>
                <a href="{{ route('feedback.create') }}"
                   class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl {{ request()->routeIs('feedback.*') ? 'bg-[#A855F7] text-white border-3 border-black shadow-[3px_3px_0px_#000000]' : 'bg-white text-black border-2 border-black shadow-[2px_2px_0px_#000000] hover:bg-[#FFE600]' }} font-display font-black tracking-wide transition">
                    <span class="material-symbols-outlined text-xl">rate_review</span>
                    <span class="text-sm uppercase">Pesan Pengembangan</span>
                </a>
            @endif

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}" class="w-full mt-2">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 rounded-xl bg-white dark:bg-slate-800 text-black dark:text-white border-2 border-black shadow-[2px_2px_0px_#000000] hover:bg-black hover:text-white dark:hover:bg-[#FFE600] dark:hover:text-black transition font-display font-bold text-xs">
                    <span class="material-symbols-outlined text-lg">logout</span>
                    <span>Log Out</span>
                </button>
            </form>
        </nav>
    </div>

    {{-- User Profile Bottom --}}
    <div class="p-3 rounded-xl bg-[#FFE600] border-3 border-black shadow-[4px_4px_0px_#000000] flex items-center gap-2.5">
        <div class="w-11 h-11 rounded-lg bg-black text-[#FFE600] flex items-center justify-center font-display font-black text-xl border-2 border-black shrink-0">
            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
        </div>
        <div class="flex flex-col leading-tight overflow-hidden">
            <span class="font-display font-black text-sm text-black truncate">{{ auth()->user()->name }}</span>
            <span class="text-[10px] font-mono font-bold text-black truncate">{{ auth()->user()->email }}</span>
        </div>
    </div>
</aside>
@endauth

{{-- ======================= MAIN CONTENT ======================= --}}
<div class="{{ auth()->check() ? 'lg:ml-[320px]' : 'w-full' }} relative z-10 min-h-screen flex flex-col flex-1 w-full overflow-x-hidden">
    {{-- Header --}}
    <header class="mx-3 sm:mx-6 mt-3 sm:mt-6 bg-white dark:bg-slate-800 text-black dark:text-white border-3 sm:border-4 border-black rounded-2xl sm:rounded-[32px] shadow-[0px_4px_0px_#000] sm:shadow-[0px_6px_0px_#000] px-4 sm:px-8 py-3 sm:py-4 flex items-center justify-between gap-3 sticky top-3 sm:top-6 z-30">
        <div class="flex items-center gap-3">
            @auth
            {{-- Hamburger Button Mobile --}}
            <button type="button" onclick="toggleMobileSidebar()" class="lg:hidden p-2 rounded-xl border-2 border-black bg-[#FFE600] hover:bg-[#FACC15] active:translate-y-1 shadow-[2px_2px_0px_#000] transition flex items-center justify-center text-black">
                <span class="material-symbols-outlined text-xl">menu</span>
            </button>
            @endauth
            <div class="overflow-hidden">
                <h2 class="font-display font-black text-base sm:text-lg text-black dark:text-white uppercase tracking-tight truncate">@yield('page-title', 'Dashboard')</h2>
                <p class="text-[11px] sm:text-xs font-mono text-stone-500 dark:text-slate-400 truncate">@yield('page-subtitle', 'GOXSGAME Platform')</p>
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            {{-- Quick Theme Toggle Button --}}
            <button type="button" onclick="toggleThemeMode()" class="p-2 rounded-xl border-2 border-black bg-white dark:bg-slate-700 hover:bg-[#FFE600] dark:hover:bg-[#FFE600] text-black dark:text-white dark:hover:text-black shadow-[2px_2px_0px_#000] flex items-center justify-center transition" title="Ganti Mode Gelap / Terang">
                <span class="material-symbols-outlined text-lg" id="global-theme-icon">dark_mode</span>
            </button>

            @auth
            <div class="bg-white dark:bg-slate-700 p-1 sm:pr-3 rounded-xl border-2 border-black shadow-[2px_2px_0px_#000000] sm:shadow-[3px_3px_0px_#000000] flex items-center gap-2">
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-[#2563EB] text-white flex items-center justify-center font-display font-black text-sm sm:text-base border-2 border-black">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="hidden sm:flex flex-col leading-tight">
                    <span class="font-display font-black text-xs text-black dark:text-white max-w-[120px] truncate">{{ auth()->user()->name ?? 'User' }}</span>
                    <span class="bg-black text-[#FFE600] text-[9px] font-mono font-bold px-1 rounded uppercase w-fit">{{ auth()->user()->role ?? 'user' }}</span>
                </div>
            </div>
            @endauth
        </div>
    </header>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mx-3 sm:mx-6 mt-4 px-4 py-3 rounded-xl bg-[#4ADE80] border-3 border-black shadow-[4px_4px_0px_#000000] font-display font-black text-black text-xs sm:text-sm flex items-center gap-2">
            <span class="material-symbols-outlined text-lg">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mx-3 sm:mx-6 mt-4 px-4 py-3 rounded-xl bg-[#FB7185] border-3 border-black shadow-[4px_4px_0px_#000000] font-bold text-black text-xs sm:text-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Main Slot --}}
    <main class="flex-1 px-3 sm:px-6 py-4 sm:py-6 w-full flex flex-col gap-4 sm:gap-6 pb-16">
        @yield('content')
    </main>
</div>

<script>
    function toggleMobileSidebar() {
        const sidebar = document.getElementById('main-sidebar');
        const backdrop = document.getElementById('mobile-sidebar-backdrop');
        if (!sidebar) return;
        
        const isHidden = sidebar.classList.contains('-translate-x-full');
        if (isHidden) {
            sidebar.classList.remove('-translate-x-full');
            if (backdrop) backdrop.classList.remove('hidden');
        } else {
            sidebar.classList.add('-translate-x-full');
            if (backdrop) backdrop.classList.add('hidden');
        }
    }

    function toggleThemeMode() {
        const isDark = document.documentElement.classList.toggle('dark');
        localStorage.setItem('goxs_theme', isDark ? 'dark' : 'light');
        updateThemeIcon();
    }

    function updateThemeIcon() {
        const isDark = document.documentElement.classList.contains('dark');
        document.querySelectorAll('.theme-toggle-icon, #global-theme-icon, #student-theme-icon').forEach(icon => {
            icon.textContent = isDark ? 'light_mode' : 'dark_mode';
        });
    }

    document.addEventListener('DOMContentLoaded', updateThemeIcon);
</script>

@stack('scripts')
</body>
</html>
