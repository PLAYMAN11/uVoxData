@props([
    'back' => null,
    'title' => 'OrientaVox',
    'titleClass' => '',
    'showMenu' => true,
])

<div class="lh-header">

    @if ($back)
        <a href="{{ $back }}" class="lh-back" aria-label="Volver">
            <svg width="22" height="22" viewBox="0 0 22 22" fill="none" aria-hidden="true">
                <path d="M13 5l-6 6 6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>
    @else
        <span style="width:22px;"></span>
    @endif

    <span class="lh-title {{ $titleClass }}">{{ $title }}</span>

    @if ($showMenu)
        <button type="button" class="lh-menu-btn" aria-label="Menú">
            <svg width="18" height="14" viewBox="0 0 18 14" fill="none" aria-hidden="true">
                <path d="M1 1h16M1 7h16M1 13h16" stroke="#1E293B" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
        </button>
    @else
        <span style="width:38px;"></span>
    @endif

</div>
