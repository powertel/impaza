@props([
    'sticky' => false,
])
<div {{ $attributes->merge(['class' => 'impaza-filter-bar' . ($sticky ? ' is-sticky' : '')]) }}>
    {{ $slot }}
    @isset($actions)
        <div class="filter-actions">{{ $actions }}</div>
    @endisset
</div>
