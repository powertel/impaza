@props([
    'title' => '',
    'subtitle' => null,
    'icon' => null,
])
<div {{ $attributes->merge(['class' => 'impaza-form-section']) }}>
    <div class="ifs-head">
        @if($icon)<span class="ifs-icon"><i class="fas {{ $icon }}"></i></span>@endif
        <div>
            <h5 class="ifs-title">{{ $title }}</h5>
            @if($subtitle)<p class="ifs-subtitle">{{ $subtitle }}</p>@endif
        </div>
    </div>
    <div class="ifs-body">
        {{ $slot }}
    </div>
</div>
