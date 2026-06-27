@props([
    'title' => null,
    'subtitle' => null,
    'stickyHeader' => true,
    'maxHeight' => null,     // e.g. '430px' to make the body scrollable
    'responsive' => true,
])
<div {{ $attributes->merge(['class' => 'impaza-table-card' . ($stickyHeader ? ' has-sticky' : '')]) }}>
    @if($title || isset($actions))
        <div class="itc-header">
            <div>
                @if($title)<h5 class="itc-title">{{ $title }}</h5>@endif
                @if($subtitle)<p class="itc-subtitle">{{ $subtitle }}</p>@endif
            </div>
            @isset($actions)<div class="itc-actions">{{ $actions }}</div>@endisset
        </div>
    @endif
    <div class="itc-body {{ $maxHeight ? 'is-scroll' : '' }}" @if($maxHeight) style="max-height: {{ $maxHeight }};" @endif>
        <div class="{{ $responsive ? 'table-responsive' : '' }}">
            {{ $slot }}
        </div>
    </div>
    @isset($footer)
        <div style="padding: 12px 16px; border-top: 1px solid var(--impaza-border);">{{ $footer }}</div>
    @endisset
</div>
