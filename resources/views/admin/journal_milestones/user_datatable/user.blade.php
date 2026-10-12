@if ($user)
    <div>
        <div class="fw-medium text-dark">{{ $user->fullname ?: 'Người dùng #' . $user->id }}</div>
        <div class="text-muted small">
            @if ($user->phone)
                <i class="ti ti-phone me-1"></i>{{ $user->phone }}
            @elseif($user->email)
                <i class="ti ti-mail me-1"></i>{{ $user->email }}
            @endif
        </div>
    </div>
@else
    <span class="text-muted">N/A</span>
@endif
