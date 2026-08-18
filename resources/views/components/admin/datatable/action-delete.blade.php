{{-- Action Delete Button --}}
@props(['route'])

<x-button.modal-delete {{ $attributes->class(['dt-action-btn dt-action-delete']) }}
    data-route="{{ $route }}"
>
    <i class="ti ti-trash"></i>
</x-button.modal-delete>
