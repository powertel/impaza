@props([
    'title' => '',
    'subtitle' => null,
    'icon' => null,
])
<div {{ $attributes->merge(['class' => 'impaza-page-header']) }}>
    <div>
        <h1 class="ph-title">
            @if($icon)<span class="ph-icon"><i class="fas {{ $icon }}"></i></span>@endif
            {{ $title }}
        </h1>
        @if($subtitle)<p class="ph-subtitle">{{ $subtitle }}</p>@endif
        {{ $slot }}
    </div>
    @isset($actions)
        <div class="ph-actions">{{ $actions }}</div>
    @endisset
</div>
