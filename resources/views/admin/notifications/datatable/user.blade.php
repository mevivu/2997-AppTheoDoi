@php
    $test = App\Models\User::find($user_id_attribute);
@endphp
@if ($test)
    <x-link target="_blank" :href="route('admin.user.edit', $test->id)" :title="$test->fullname" class="text-decoration-none" />
@else
    <span class="text-muted">
        N/A
    </span>
@endif
