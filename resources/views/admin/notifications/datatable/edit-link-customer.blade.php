@php
    $user = App\Models\User::find($user_id);
@endphp
@if ($user)
    <x-link :href="route('admin.user.edit', $user->id)" :title="$user->fullname" class="text-decoration-none" />
@else
    <span class="text-muted">
        N/A
    </span>
@endif
