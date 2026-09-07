@php
    $activeCount = $user->activeDevices()->count();
    $maxAllowed = $user->getMaxDevicesAllowed();
    $activePackage = $user->userPackages()->where('status', \App\Enums\Package\PackageUserStatus::Active)->where('end_date', '>=', now())->latest('end_date')->first();
    $packageName = $activePackage?->package?->name ?? 'Gói Cơ Bản (Free)';
    $devices = $user->devices()->latest('last_active_at')->get();
@endphp

<div class="device-management-section">
    <!-- Device Quota Summary Banner -->
    <div class="card border rounded-3 p-3 mb-4" style="background-color: #f8fafc; border-color: #e2e8f0 !important;">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 bg-azure-lt rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="ti ti-devices fs-2 text-azure"></i>
                </div>
                <div>
                    <h5 class="mb-1 fw-bold text-dark d-flex align-items-center gap-2">
                        <span>{{ __('Quản lý Thiết Bị Đăng Nhập') }}</span>
                        <span id="device-quota-badge" class="badge {{ $activeCount >= $maxAllowed ? 'bg-danger text-white' : 'bg-success text-white' }} px-2 py-1 fs-12">
                            <span id="active-device-count">{{ $activeCount }}</span> / <span id="max-device-count">{{ $maxAllowed }}</span> {{ __('thiết bị') }}
                        </span>
                    </h5>
                    <p class="mb-0 text-muted fs-13">
                        {{ __('Gói hiện tại:') }} <strong class="text-primary">{{ $packageName }}</strong> &bull; 
                        <span id="device-quota-status-text">
                            @if($activeCount >= $maxAllowed)
                                <span class="text-danger fw-semibold">{{ __('Đã đạt giới hạn tối đa. Cần giải phóng thiết bị cũ trước khi liên kết máy mới.') }}</span>
                            @else
                                <span class="text-success fw-semibold">{{ __('Còn trống :count slot liên kết thiết bị.', ['count' => $maxAllowed - $activeCount]) }}</span>
                            @endif
                        </span>
                    </p>
                </div>
            </div>

            <div id="wrap-revoke-all-btn" style="{{ $activeCount > 0 ? '' : 'display: none !important;' }}">
                <button type="button" 
                        class="btn btn-outline-danger btn-sm d-flex align-items-center gap-1 btn-revoke-all-devices"
                        data-url="{{ route('admin.user.device.revokeAll', $user->id) }}"
                        data-user-name="{{ $user->fullname }}">
                    <i class="ti ti-device-mobile-off"></i>
                    <span>{{ __('Giải phóng toàn bộ thiết bị') }}</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Device List Table -->
    <div class="table-responsive border rounded-3">
        <table class="table table-vcenter table-hover mb-0" id="table-user-devices">
            <thead class="table-light">
                <tr>
                    <th class="text-muted fs-12 fw-bold text-uppercase">{{ __('Tên thiết bị') }}</th>
                    <th class="text-muted fs-12 fw-bold text-uppercase">{{ __('Mã thiết bị (Device ID)') }}</th>
                    <th class="text-muted fs-12 fw-bold text-uppercase">{{ __('IP đăng nhập') }}</th>
                    <th class="text-muted fs-12 fw-bold text-uppercase">{{ __('Lần cuối hoạt động') }}</th>
                    <th class="text-muted fs-12 fw-bold text-uppercase text-center">{{ __('Trạng thái') }}</th>
                    <th class="text-muted fs-12 fw-bold text-uppercase text-center" style="width: 140px;">{{ __('Hành động') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($devices as $device)
                    <tr id="device-row-{{ $device->id }}">
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div id="device-avatar-{{ $device->id }}" class="avatar avatar-sm {{ $device->is_active ? 'bg-primary-lt text-primary' : 'bg-secondary-lt text-secondary' }} rounded-circle">
                                    <i class="ti ti-device-mobile fs-3"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark fs-13">
                                        {{ $device->device_name ?: __('Thiết bị di động') }}
                                    </div>
                                    <div class="text-muted fs-11">
                                        {{ __('Liên kết:') }} {{ format_datetime($device->created_at) }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <code class="text-dark fs-12 p-1 bg-light rounded" title="{{ $device->device_id }}">
                                {{ \Illuminate\Support\Str::limit($device->device_id, 24) }}
                            </code>
                        </td>
                        <td>
                            <span class="text-muted fs-12">
                                <i class="ti ti-world me-1"></i>{{ $device->ip_address ?: 'N/A' }}
                            </span>
                        </td>
                        <td>
                            <span class="fs-12 text-muted">
                                <i class="ti ti-clock me-1"></i>{{ $device->last_active_at ? format_datetime($device->last_active_at) : __('Chưa ghi nhận') }}
                            </span>
                        </td>
                        <td class="text-center" id="device-status-col-{{ $device->id }}">
                            @if($device->is_active)
                                <span class="badge bg-success-lt px-2 py-1 badge-device-status">
                                    <i class="ti ti-circle-check me-1"></i>{{ __('Đang liên kết') }}
                                </span>
                            @else
                                <span class="badge bg-secondary-lt px-2 py-1 badge-device-status">
                                    <i class="ti ti-ban me-1"></i>{{ __('Đã giải phóng') }}
                                </span>
                            @endif
                        </td>
                        <td class="text-center" id="device-action-col-{{ $device->id }}">
                            @if($device->is_active)
                                <button type="button" 
                                        class="btn btn-sm btn-outline-danger px-2 py-1 d-inline-flex align-items-center gap-1 btn-revoke-device" 
                                        data-url="{{ route('admin.user.device.revoke', [$user->id, $device->id]) }}"
                                        data-device-id="{{ $device->id }}"
                                        data-device-name="{{ $device->device_name ?: __('Thiết bị di động') }}"
                                        title="{{ __('Giải phóng / Hủy liên kết thiết bị này') }}">
                                    <i class="ti ti-unlink"></i>
                                    <span>{{ __('Giải phóng') }}</span>
                                </button>
                            @else
                                <span class="text-muted fs-12 fst-italic">{{ __('Không khả dụng') }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr id="device-empty-row">
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="ti ti-device-mobile-off fs-1 d-block mb-2 text-secondary"></i>
                            <span>{{ __('Chưa có thiết bị nào được liên kết với tài khoản này.') }}</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
