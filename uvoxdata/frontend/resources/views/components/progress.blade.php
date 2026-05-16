@props([
    'step' => 1,
    'total' => 3,
    'variant' => 'primary',
    'showCheck' => null,
])

@php
    $pct = max(0, min(100, ($step / max($total, 1)) * 100));
    $check = is_null($showCheck) ? ($step >= $total) : $showCheck;
    $fillClass = $variant === 'danger' ? 'danger' : '';
@endphp

<div class="lh-progress-wrap">
    <span class="lh-step-label">Paso {{ $step }} de {{ $total }}</span>
    <div class="lh-progress-row">
        <div class="lh-progress-track">
            <div class="lh-progress-fill {{ $fillClass }}" style="width: {{ $pct }}%"></div>
        </div>

        @if ($check)
            <svg class="lh-progress-check" viewBox="0 0 22 22" fill="none" aria-hidden="true">
                <circle cx="11" cy="11" r="10" stroke="#22C55E" stroke-width="1.8"/>
                <path d="M6.5 11l3 3 6-6" stroke="#22C55E" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        @endif
    </div>
</div>
