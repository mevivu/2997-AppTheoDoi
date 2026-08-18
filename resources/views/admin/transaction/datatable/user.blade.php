@if ($user)
    <x-link target="_blank" :href="route('admin.user.edit', $user->id)" class="d-inline-flex align-items-center gap-1.5 text-decoration-none fw-semibold text-primary">
        <i class="ti ti-user fs-14 opacity-75"></i>
        <span>{{ $user->fullname ?? 'Khách hàng #' . $user->id }}</span>
    </x-link>
@else
    <span class="text-muted fst-italic">Không xác định</span>
@endif
