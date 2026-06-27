@props([
    'id',
    'title' => '',
    'icon' => null,
    'size' => null,          // sm | lg | xl
    'centered' => true,
    'scrollable' => true,
    'static' => false,       // backdrop static (no dismiss on outside click)
])
<div class="modal fade impaza-modal" id="{{ $id }}" tabindex="-1" aria-hidden="true"
     @if($static) data-bs-backdrop="static" data-bs-keyboard="false" @endif
     aria-labelledby="{{ $id }}Label">
    <div class="modal-dialog {{ $centered ? 'modal-dialog-centered' : '' }} {{ $scrollable ? 'modal-dialog-scrollable' : '' }} {{ $size ? 'modal-'.$size : '' }}">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $id }}Label">
                    @if($icon)<span class="imh-icon"><i class="fas {{ $icon }}"></i></span>@endif
                    {{ $title }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{ $slot }}
            </div>
            @isset($footer)
                <div class="modal-footer">{{ $footer }}</div>
            @endisset
        </div>
    </div>
</div>
