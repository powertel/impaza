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

    $hex6 = null;
    if (preg_match('/^#([0-9a-fA-F]{6})([0-9a-fA-F]{2})?$/', (string) $resolved, $m)) {
        $hex6 = '#' . $m[1];
    }

    $darken = function ($hex, $factor = 0.55) {
        if (!preg_match('/^#([0-9a-fA-F]{6})$/', (string) $hex, $m)) {
            return $hex;
        }
        $r = (int) round(hexdec(substr($m[1], 0, 2)) * $factor);
        $g = (int) round(hexdec(substr($m[1], 2, 2)) * $factor);
        $b = (int) round(hexdec(substr($m[1], 4, 2)) * $factor);
        return sprintf('#%02X%02X%02X', max(0, min(255, $r)), max(0, min(255, $g)), max(0, min(255, $b)));
    };

    $baseColor = $hex6 ?? $resolved;
    $softTextColor = $hex6 ? $darken($hex6, 0.50) : $resolved;

    $textColor = '#0F172A';
    if ($hex6 !== null && preg_match('/^#([0-9a-fA-F]{6})$/', $hex6, $m)) {
        $r = hexdec(substr($m[1], 0, 2));
        $g = hexdec(substr($m[1], 2, 2));
        $b = hexdec(substr($m[1], 4, 2));
        $lum = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
        $textColor = $lum > 0.6 ? '#0F172A' : '#FFFFFF';
    }
@endphp
@if($soft)
    <span {{ $attributes->merge(['class' => 'impaza-badge']) }}
          style="color: {{ $softTextColor }}; background: color-mix(in srgb, {{ $baseColor }} 18%, transparent); border: 1px solid color-mix(in srgb, {{ $baseColor }} 34%, transparent); font-weight: 600;">{{ $label !== '' ? $label : $slot }}</span>
@else
    <span {{ $attributes->merge(['class' => 'impaza-badge']) }}
          style="background-color: {{ $baseColor }}; color: {{ $textColor }};">{{ $label !== '' ? $label : $slot }}</span>
@endif
