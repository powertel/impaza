@props([
    'title' => '',
    'value' => '',
    'icon' => 'fa-chart-bar',
    'variant' => 'primary',      // primary | success | warning | danger | info | muted
    'href' => null,
    'sub' => null,               // descriptive sub-text (shown when no trend given)
    'trend' => null,             // signed integer percent, or null to hide
    'trendLabel' => 'vs last month',
])
@php
    $variants = [
        'primary' => 'var(--impaza-primary)',
        'success' => 'var(--impaza-success)',
        'warning' => 'var(--impaza-warning)',
        'danger'  => 'var(--impaza-danger)',
        'info'    => 'var(--impaza-info)',
        'muted'   => 'var(--impaza-muted)',
    ];
    $accent = $variants[$variant] ?? $variants['primary'];
    $hasTrend = $trend !== null && $trend !== '';
    $trendDir = $hasTrend ? ((int) $trend > 0 ? 'up' : ((int) $trend < 0 ? 'down' : 'flat')) : 'flat';
    $trendIcon = $trendDir === 'up' ? 'fa-arrow-trend-up' : ($trendDir === 'down' ? 'fa-arrow-trend-down' : 'fa-minus');
    $innerAttributes = $attributes->merge(['class' => 'impaza-stat']);
    $hasSpark = trim((string) $slot) !== '';
@endphp
@if($href)
    <a href="{{ $href }}" {{ $innerAttributes }} style="--impaza-stat-accent: {{ $accent }};">
@else
    <div {{ $innerAttributes }} style="--impaza-stat-accent: {{ $accent }};">
@endif
        <div class="impaza-stat-head">
            <span class="impaza-stat-icon"><i class="fas {{ $icon }}"></i></span>
            <span class="impaza-stat-title">{{ $title }}</span>
        </div>
        <div class="impaza-stat-body">
            <div class="impaza-stat-metric">
                <div class="impaza-stat-value">{{ $value }}</div>
                @if($hasTrend)
                    <div class="impaza-stat-subline">
                        <span class="impaza-stat-trend impaza-stat-trend--{{ $trendDir }}">
                            <i class="fas {{ $trendIcon }}"></i>{{ (int) $trend > 0 ? '+' : '' }}{{ (int) $trend }}%
                        </span>
                        <span class="impaza-stat-sublabel">{{ $trendLabel }}</span>
                    </div>
                @elseif($sub !== null)
                    <div class="impaza-stat-sub">{{ $sub }}</div>
                @endif
            </div>
            @if($hasSpark)
                <div class="impaza-stat-spark">{{ $slot }}</div>
            @endif
        </div>
@if($href)
    </a>
@else
    </div>
@endif
