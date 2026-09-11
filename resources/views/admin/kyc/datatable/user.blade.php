@php
    use App\AES\AESHelper;

    // 1. Giải mã số điện thoại đã được mã hóa bằng AES
    $phone = $user?->phone;
    if (!empty($phone)) {
        try {
            $decryptedPhone = AESHelper::decrypt($phone);
            if ($decryptedPhone !== false && !empty($decryptedPhone)) {
                $phone = $decryptedPhone;
            }
        } catch (\Throwable $e) {
            // Giữ nguyên nếu giải mã lỗi
        }
    }

    // 2. Chuẩn hóa đường dẫn hình ảnh đại diện
    $avatarUrl = null;
    $rawAvatar = $user?->avatar;
    if (!empty($rawAvatar)) {
        if (str_starts_with($rawAvatar, 'http://') || str_starts_with($rawAvatar, 'https://')) {
            $avatarUrl = $rawAvatar;
        } elseif (str_starts_with($rawAvatar, 'public/')) {
            $avatarUrl = asset($rawAvatar);
        } elseif (str_starts_with($rawAvatar, '/public/')) {
            $avatarUrl = asset(ltrim($rawAvatar, '/'));
        } elseif (str_starts_with($rawAvatar, 'storage/')) {
            $avatarUrl = asset($rawAvatar);
        } elseif (str_starts_with($rawAvatar, '/storage/')) {
            $avatarUrl = asset(ltrim($rawAvatar, '/'));
        } elseif (str_starts_with($rawAvatar, '/')) {
            $avatarUrl = asset(ltrim($rawAvatar, '/'));
        } else {
            $avatarUrl = asset('public/' . $rawAvatar);
        }
    }
@endphp

@if ($user)
    <div class="d-flex align-items-center gap-2.5 py-1 text-start">
        <div class="avatar-wrapper flex-shrink-0 position-relative" style="width: 42px; height: 42px; min-width: 42px;">
            @if ($avatarUrl)
                <img src="{{ $avatarUrl }}" 
                     alt="" 
                     class="rounded-circle border shadow-2xs" 
                     style="width: 42px; height: 42px; object-fit: cover;"
                     onerror="this.style.display='none'; this.nextElementSibling.classList.remove('d-none');">
                <div class="rounded-circle bg-primary-lt d-flex align-items-center justify-content-center fw-bold text-primary border shadow-2xs d-none" 
                     style="width: 42px; height: 42px; font-size: 15px;">
                    {{ mb_substr($user->fullname ?? 'U', 0, 1) }}
                </div>
            @else
                <div class="rounded-circle bg-primary-lt d-flex align-items-center justify-content-center fw-bold text-primary border shadow-2xs" 
                     style="width: 42px; height: 42px; font-size: 15px;">
                    {{ mb_substr($user->fullname ?? 'U', 0, 1) }}
                </div>
            @endif
        </div>
        <div class="d-flex flex-column min-w-0" style="line-height: 1.4;">
            <a href="{{ route('admin.user.edit', $user->id) }}" 
               target="_blank" 
               class="text-decoration-none fw-bold text-dark text-truncate d-flex align-items-center gap-1 hover-primary"
               title="{{ __('Xem chi tiết đối tác') }}">
                <span>{{ $user->fullname ?? 'Khách hàng #' . $user->id }}</span>
                <i class="ti ti-external-link fs-11 text-muted"></i>
            </a>
            <div class="text-muted fs-11 font-monospace text-nowrap">
                <i class="ti ti-phone me-0.5"></i>{{ $phone ?: '-' }}
            </div>
            <div class="mt-0.5">
                <span class="badge {{ $user->getAffiliateRankBadge() }} px-1.5 py-0.5 fs-10 fw-semibold">
                    <i class="ti ti-award me-0.5"></i>{{ $user->getAffiliateRankName() }}
                </span>
            </div>
        </div>
    </div>
@else
    <span class="text-muted fst-italic">-</span>
@endif
