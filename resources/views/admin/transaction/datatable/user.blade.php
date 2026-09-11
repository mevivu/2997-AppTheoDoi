@if ($user)
    <div class="d-flex flex-column gap-1">
        <x-link target="_blank" :href="route('admin.user.edit', $user->id)" class="d-inline-flex align-items-center gap-1.5 text-decoration-none fw-semibold text-primary">
            <i class="ti ti-user fs-14 opacity-75"></i>
            <span>{{ $user->fullname ?? 'Khách hàng #' . $user->id }}</span>
        </x-link>
        <div>
            @if ($user->hasCompletedKyc())
                <span class="badge bg-success-lt fs-11 py-0 px-1.5" title="Đã duyệt CCCD & MST">
                    <i class="ti ti-shield-check"></i> Đã duyệt CCCD
                </span>
            @elseif ($user->isKycPending())
                <a href="{{ route('admin.kyc.index', ['status' => 'pending']) }}" target="_blank" class="badge bg-warning-lt fs-11 py-0 px-1.5 text-decoration-none" title="Đang chờ duyệt CCCD">
                    <i class="ti ti-clock"></i> Chờ duyệt CCCD
                </a>
            @elseif ($user->isKycRejected())
                <a href="{{ route('admin.kyc.index', ['status' => 'rejected']) }}" target="_blank" class="badge bg-danger-lt fs-11 py-0 px-1.5 text-decoration-none" title="Bị từ chối CCCD">
                    <i class="ti ti-shield-x"></i> Từ chối CCCD
                </a>
            @else
                <span class="badge bg-secondary-lt fs-11 py-0 px-1.5" title="Chưa nộp CCCD">
                    <i class="ti ti-shield-minus"></i> Chưa nộp CCCD
                </span>
            @endif
        </div>
    </div>
@else
    <span class="text-muted fst-italic">Không xác định</span>
@endif
