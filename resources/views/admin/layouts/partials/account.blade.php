@php
    use App\Traits\RouteAdminSystem;
    $user = auth('admin')->user();
    $roleName = $user->roles->first()?->name ?? 'Quản trị viên';
    $avatarUrl = asset($user->avatar ?? config('custom.images.avatar', '/public/assets/images/avatar-user.png'));
@endphp

<div class="nav-item dropdown custom-user-dropdown">
    <a href="#" class="nav-link user-trigger-link d-flex align-items-center px-2 py-1 rounded-3" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Open user menu">
        <span class="avatar avatar-sm rounded-circle me-2 flex-shrink-0" style="background-image: url('{{ $avatarUrl }}'); width: 34px; height: 34px; background-size: cover; background-position: center; border: 1px solid #e2e8f0;"></span>
        <div class="d-none d-xl-block text-start me-1.5 lh-1">
            <div class="user-fullname fw-bold text-dark fs-13 mb-0.5">{{ $user->fullname ?? 'Mevivu Hỗ Trợ' }}</div>
            <div class="user-role-text text-muted fs-11 fw-medium">{{ $roleName }}</div>
        </div>
        <i class="ti ti-chevron-down text-muted fs-12 ms-1 d-none d-xl-inline-block opacity-75"></i>
    </a>

    <div class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3 p-2 mt-2 dropdown-menu-animated" style="min-width: 190px;">
        {{-- Dropdown Actions --}}
        <div class="box-item-dropdown">
            <a href="{{ route(RouteAdminSystem::PROFILE_INDEX) }}" class="dropdown-item custom-dropdown-item rounded-2 py-1.5 px-2.5 mb-1">
                <div class="item-icon-box bg-primary-subtle text-primary rounded-2 d-flex align-items-center justify-content-center me-2" style="width: 28px; height: 28px;">
                    <i class="ti ti-user fs-14"></i>
                </div>
                <span class="fw-medium text-dark fs-13">{{ __('Trang cá nhân') }}</span>
            </a>

            <a href="{{ route(RouteAdminSystem::PASSWORD_INDEX) }}" class="dropdown-item custom-dropdown-item rounded-2 py-1.5 px-2.5 mb-1">
                <div class="item-icon-box bg-warning-subtle text-warning rounded-2 d-flex align-items-center justify-content-center me-2" style="width: 28px; height: 28px;">
                    <i class="ti ti-key fs-14"></i>
                </div>
                <span class="fw-medium text-dark fs-13">{{ __('Đổi mật khẩu') }}</span>
            </a>

            <div class="dropdown-divider my-1 opacity-50"></div>

            <a href="#" class="dropdown-item custom-dropdown-item logout-item rounded-2 py-1.5 px-2.5" data-bs-toggle="modal" data-bs-target="#modalLogout">
                <div class="item-icon-box bg-danger-subtle text-danger rounded-2 d-flex align-items-center justify-content-center me-2" style="width: 28px; height: 28px;">
                    <i class="ti ti-logout fs-14"></i>
                </div>
                <span class="fw-medium text-danger fs-13">{{ __('Đăng xuất') }}</span>
            </a>
        </div>
    </div>
</div>

<style>
    .custom-user-dropdown .user-trigger-link {
        transition: background-color 0.15s ease;
        text-decoration: none;
    }

    .custom-user-dropdown .user-trigger-link:hover,
    .custom-user-dropdown .user-trigger-link[aria-expanded="true"] {
        background-color: #f1f5f9;
    }

    .custom-user-dropdown .dropdown-menu {
        box-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.12), 0 0 1px rgba(15, 23, 42, 0.1) !important;
        border: 1px solid #e2e8f0 !important;
        animation: fadeInFast 0.15s ease-out;
    }

    @keyframes fadeInFast {
        from {
            opacity: 0;
            transform: translateY(4px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .custom-dropdown-item {
        display: flex;
        align-items: center;
        transition: background-color 0.15s ease;
        color: #334155;
        text-decoration: none;
    }

    .custom-dropdown-item:hover {
        background-color: #f8fafc !important;
    }

    .custom-dropdown-item.logout-item:hover {
        background-color: #fef2f2 !important;
    }

    .item-icon-box {
        flex-shrink: 0;
    }

    .bg-primary-subtle {
        background-color: #eff6ff !important;
    }

    .bg-warning-subtle {
        background-color: #fffbeb !important;
    }

    .bg-danger-subtle {
        background-color: #fef2f2 !important;
    }
</style>
