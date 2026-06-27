@props([
    'label' => '',
    'color' => null,       // explicit CSS color / hex (e.g. from Status::STATUS_COLOR)
    'variant' => null,     // semantic: open|success|warning|danger|info|muted (used if no color)
    'soft' => false,       // soft tinted pill instead of solid fill
])
@php
    $semantic = [
        'open'    => '#6366F1',
        'primary' => '#6366F1',
        'success' => '#10B981',
        'warning' => '#F59E0B',
        'danger'  => '#EF4444',
        'info'    => '#06B6D4',
        'muted'   => '#64748B',
    ];
    $resolved = $color ?: ($semantic[$variant] ?? '#64748B');

    // Auto-contrast text for solid fill when a hex color is supplied.
    $textColor = '#0F172A';
    if (preg_match('/^#([0-9a-fA-F]{6})$/', (string) $resolved, $m)) {
        $r = hexdec(substr($m[1], 0, 2));
        $g = hexdec(substr($m[1], 2, 2));
        $b = hexdec(substr($m[1], 4, 2));
        $lum = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
        $textColor = $lum > 0.6 ? '#0F172A' : '#FFFFFF';
    }
@endphp
@if($soft)
    <span {{ $attributes->merge(['class' => 'impaza-badge']) }}
          style="color: {{ $resolved }}; background: color-mix(in srgb, {{ $resolved }} 16%, transparent); border: 1px solid color-mix(in srgb, {{ $resolved }} 28%, transparent);">{{ $label !== '' ? $label : $slot }}</span>
@else
    <span {{ $attributes->merge(['class' => 'impaza-badge']) }}
          style="background-color: {{ $resolved }}; color: {{ $textColor }};">{{ $label !== '' ? $label : $slot }}</span>
@endif
