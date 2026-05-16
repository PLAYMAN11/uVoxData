@props([
    'title' => '',
    'tone' => 'purple',
    'open' => true,
])

@php
    $tone = in_array($tone, ['purple', 'yellow', 'green']) ? $tone : 'purple';
    $openAttr = $open ? 'true' : 'false';
@endphp

<section class="res-section" data-open="{{ $openAttr }}" data-collapsible>
    <button type="button" class="res-section-head" aria-expanded="{{ $openAttr }}">
        <div class="res-section-icon {{ $tone }}">
            {{ $icon ?? '' }}
        </div>

        <h3 class="res-section-title">{{ $title }}</h3>

        <svg class="res-section-toggle" width="14" height="8" viewBox="0 0 14 8" fill="none" aria-hidden="true">
            <path d="M1 1l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>

    {{ $slot }}
</section>
