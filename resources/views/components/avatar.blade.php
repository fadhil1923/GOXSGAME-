@props(['code', 'size' => 'md'])

@php
    $baseMap = [
        'cow' => '🐮',
        'horse' => '🐴',
        'goat' => '🐐',
        'pig' => '🐷',
        'bear' => '🐻',
        'fox' => '🦊',
        'rabbit' => '🐰',
        'dog' => '🐶',
        'cat' => '🐱',
    ];

    $accMap = [
        'cap' => '🧢',
        'glasses' => '🕶️',
        'helmet' => '🪖',
        'crown' => '👑',
        'tophat' => '🎩',
        'toga' => '🎓',
        'bow' => '🎀',
        'none' => '',
    ];

    $parts = explode('|', $code ?? 'cow|none');
    $base = $parts[0] ?? 'cow';
    $acc = $parts[1] ?? 'none';

    $baseEmoji = $baseMap[$base] ?? '🐮';
    $accEmoji = $accMap[$acc] ?? '';

    // Styling per size
    $sizes = [
        'sm' => ['base' => 'text-2xl', 'acc' => 'text-xl -top-1'],
        'md' => ['base' => 'text-4xl', 'acc' => 'text-3xl -top-2'],
        'lg' => ['base' => 'text-6xl', 'acc' => 'text-5xl -top-4'],
        'xl' => ['base' => 'text-[7rem]', 'acc' => 'text-[6rem] -top-6'],
    ];

    $css = $sizes[$size] ?? $sizes['md'];
@endphp

<div class="relative inline-flex items-center justify-center pointer-events-none select-none {{ $attributes->get('class') }}" style="line-height: 1; font-family: 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', sans-serif;">
    <span class="{{ $css['base'] }} relative z-0">{{ $baseEmoji }}</span>
    @if($accEmoji)
    <span class="absolute left-1/2 -translate-x-1/2 {{ $css['acc'] }} z-10 drop-shadow-md">{{ $accEmoji }}</span>
    @endif
</div>
