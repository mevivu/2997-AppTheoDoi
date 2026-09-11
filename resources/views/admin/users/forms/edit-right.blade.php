@php use App\Traits\RouteAdminSystem; @endphp
<div class="col-12 col-lg-4 col-xl-3">
    <!-- Card 1: Trạng thái tài khoản -->
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                <i class="ti ti-toggle-right text-primary me-2 fs-4"></i>
                {{ __('Trạng thái tài khoản') }} <span class="text-danger ms-1">*</span>
            </h5>
        </div>
        <div class="card-body p-4">
            <x-select name="status" :required="true">
                @foreach ($status as $key => $value)
                    <x-select-option :option="$user->status->value" :value="$key" :title="__($value)" />
                @endforeach
            </x-select>
        </div>
    </div>

    <!-- Card 2: Ảnh đại diện -->
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                <i class="ti ti-photo-heart text-primary me-2 fs-4"></i>
                {{ __('Ảnh đại diện') }}
            </h5>
        </div>
        <div class="card-body p-4 text-center">
            <div class="settings-logo-upload-wrapper text-center my-2 p-3">
                <x-input-image-ckfinder name="avatar" showImage="avatar" class="img-fluid" :value="$user->avatar" />
            </div>
            <p class="text-muted fs-12 mb-0">{{ __('Hỗ trợ định dạng JPG, PNG, WEBP. Nhấp vào ảnh để tải hoặc thay đổi qua CKFinder.') }}</p>
        </div>
    </div>

    <!-- Card 3: Thao tác & Quản lý nhanh -->
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                <i class="ti ti-bolt text-warning me-2 fs-4"></i>
                {{ __('Thao tác nhanh') }}
            </h5>
        </div>
        <div class="card-body p-3 d-flex flex-column gap-2">
            <button type="button" class="btn btn-outline-success open-modal-deposit d-flex align-items-center justify-content-center gap-2 py-2"
                    data-id="{{ $user->id }}"
                    data-fullname="{{ $user->fullname ?? '' }}"
                    data-code="{{ $user->code ?? ('#' . $user->id) }}"
                    data-balance="{{ (float)($user->wallet_balance ?? 0) }}"
                    title="{{ __('Nạp tiền vào ví thành viên') }}">
                <i class="ti ti-wallet fs-4"></i>
                <span>{{ __('Nạp tiền vào ví') }}</span>
            </button>

            <button type="button" class="btn btn-outline-danger open-modal-withdraw d-flex align-items-center justify-content-center gap-2 py-2"
                    data-id="{{ $user->id }}"
                    data-fullname="{{ $user->fullname ?? '' }}"
                    data-code="{{ $user->code ?? ('#' . $user->id) }}"
                    data-balance="{{ (float)($user->wallet_balance ?? 0) }}"
                    title="{{ __('Rút / Trừ tiền từ ví thành viên') }}">
                <i class="ti ti-cash-off fs-4"></i>
                <span>{{ __('Rút / Trừ tiền ví') }}</span>
            </button>

            <a href="{{ route('admin.user.history', $user->id) }}" class="btn btn-outline-primary d-flex align-items-center justify-content-center gap-2 py-2">
                <i class="ti ti-receipt fs-4"></i>
                <span>{{ __('Lịch sử giao dịch') }}</span>
            </a>

            <button type="button" class="btn btn-outline-danger d-flex align-items-center justify-content-center gap-2 py-2"
                    data-bs-toggle="modal" data-bs-target="#modalClearTokens">
                <i class="ti ti-device-mobile-off fs-4"></i>
                <span>{{ __('Đăng xuất thiết bị') }}</span>
            </button>

            <button type="button" class="btn btn-outline-danger open-modal-force-delete d-flex align-items-center justify-content-center gap-2 py-2"
                    data-route="{{ route(RouteAdminSystem::USER_FORCE_DELETE, $user->id) }}"
                    data-fullname="{{ $user->fullname }}"
                    data-code="{{ $user->code }}"
                    data-bs-toggle="modal" data-bs-target="#modalForceDelete"
                    title="{{ __('Xóa vĩnh viễn tài khoản này') }}">
                <i class="ti ti-trash-x fs-4"></i>
                <span>{{ __('Xóa tài khoản vĩnh viễn') }}</span>
            </button>
        </div>
    </div>


    {{-- Floating Form Actions --}}
    <x-admin.form-actions
        :submit-title="__('Lưu thay đổi')"
        submit-icon="ti ti-device-floppy"
        :back-route="route(RouteAdminSystem::USER_INDEX)"
        :back-title="__('Quay lại')"
    />
</div>

<!-- Modal Đăng xuất thiết bị -->
<div class="modal modal-blur fade" id="modalClearTokens" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content custom-confirm-modal">
            <div class="modal-body">
                <div class="modal-icon-badge badge-warning">
                    <i class="ti ti-device-mobile-off"></i>
                </div>
                <div class="modal-title">{{ __('Đăng xuất thiết bị?') }}</div>
                <p class="modal-desc">{{ __('Bạn có chắc chắn muốn đăng xuất tài khoản này khỏi toàn bộ thiết bị đang đăng nhập?') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">{{ __('Hủy') }}</button>
                <form action="{{ route('admin.user.clearNormalTokens') }}" method="POST" class="m-0 flex-grow-1">
                    @csrf
                    <button type="submit" class="btn btn-warning w-100">{{ __('Xác nhận') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
