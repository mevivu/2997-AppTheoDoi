@php
    $user = $feedback->user;
    $email = $feedback->email ?: $user?->decrypted_email;
@endphp

<div class="d-flex flex-column py-1 text-start" style="line-height: 1.45;">
    <div class="d-flex align-items-center gap-1.5">
        <span class="badge bg-teal-lt text-teal font-monospace fw-bold px-2 py-0.5 fs-11">
            ID #{{ $feedback->user_id }}
        </span>
        @if ($user)
            <a href="{{ route('admin.user.edit', $user->id) }}"
               target="_blank"
               class="text-decoration-none fw-bold text-dark text-truncate d-inline-flex align-items-center gap-1"
               title="{{ __('Xem hồ sơ tài khoản') }}">
                <span>{{ $user->fullname ?? 'User #' . $feedback->user_id }}</span>
                <i class="ti ti-external-link fs-11 text-muted"></i>
            </a>
        @endif
    </div>
    <div class="text-muted fs-12 font-monospace mt-1">
        <i class="ti ti-mail me-1"></i>{{ $email ?: '-' }}
    </div>
</div>
