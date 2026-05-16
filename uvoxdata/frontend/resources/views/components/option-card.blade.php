@props([
    'title' => '',
    'subtitle' => '',
    'href' => null,
    'iconTone' => 'default',
    'tag' => null,
    'attrs' => [],
])

@php
    $tone = in_array($iconTone, ['red', 'orange', 'purple', 'green', 'yellow']) ? $iconTone : '';
    $element = $tag ?? ($href ? 'a' : 'button');
    $base = ['class' => 'lh-card'];
    if ($element === 'a') {
        $base['href'] = $href;
    } elseif ($element === 'button') {
        $base['type'] = 'button';
    }
    $merged = array_merge($base, $attrs);
@endphp

<{{ $element }}
    @foreach ($merged as $k => $v)
        {{ $k }}="{{ $v }}"
    @endforeach
    {{ $attributes }}
>
    <div class="lh-card-icon {{ $tone }}">
        {{ $icon ?? '' }}
    </div>

    <div class="lh-card-body">
        <p class="lh-card-title">{{ $title }}</p>
        @if ($subtitle)
            <p class="lh-card-sub">{{ $subtitle }}</p>
        @endif
    </div>

    <svg class="lh-chevron" width="9" height="15" viewBox="0 0 9 15" fill="none" aria-hidden="true">
        <path d="M1 1l7 6.5L1 14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
</{{ $element }}>
