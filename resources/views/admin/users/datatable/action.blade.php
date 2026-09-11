@php use App\Traits\RouteAdminSystem; @endphp

<x-admin.datatable.action-group>
    <button type="button"
            class="dt-action-btn text-success open-modal-deposit"
            data-id="{{ $id }}"
            data-fullname="{{ $fullname ?? '' }}"
            data-code="{{ $code ?? '' }}"
            data-balance="{{ (float)($wallet_balance ?? 0) }}"
            title="{{ __('Nạp tiền vào ví') }}">
        <i class="ti ti-wallet"></i>
    </button>
    <button type="button"
            class="dt-action-btn text-danger open-modal-withdraw"
            data-id="{{ $id }}"
            data-fullname="{{ $fullname ?? '' }}"
            data-code="{{ $code ?? '' }}"
            data-balance="{{ (float)($wallet_balance ?? 0) }}"
            title="{{ __('Rút / Trừ tiền từ ví') }}">
        <i class="ti ti-cash-off"></i>
    </button>
    <x-admin.datatable.action-edit :href="route(RouteAdminSystem::USER_EDIT, $id)" />
    <button type="button"
            class="dt-action-btn dt-action-deactivate open-modal-deactivate"
            data-route="{{ route(RouteAdminSystem::USER_DELETE, $id) }}"
            data-id="{{ $id }}"
            data-fullname="{{ $fullname ?? '' }}"
            data-code="{{ $code ?? '' }}"
            data-bs-toggle="modal"
            data-bs-target="#modalDeactivateUser"
            title="{{ __('Ngưng hoạt động tài khoản (Cập nhật trạng thái)') }}">
        <i class="ti ti-user-off"></i>
    </button>
    <x-admin.datatable.action-force-delete 
        :route="route(RouteAdminSystem::USER_FORCE_DELETE, $id)" 
        data-fullname="{{ $fullname ?? '' }}"
        data-code="{{ $code ?? '' }}"
    />
</x-admin.datatable.action-group>

