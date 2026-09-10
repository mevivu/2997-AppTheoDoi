{{-- Action Force Delete Button --}}
@props(['route'])

<button type="button"
    {{ $attributes->class(['dt-action-btn dt-action-force-delete open-modal-force-delete']) }}
    data-route="{{ $route }}"
    data-bs-toggle="modal"
    data-bs-target="#modalForceDelete"
    title="{{ __('Xóa tài khoản vĩnh viễn') }}"
>
    <i class="ti ti-trash-x"></i>
</button>
