@php
    $test = App\Models\Admin::find($admin_id);
@endphp
@if ($test)
    <x-link :href="route('admin.admin.edit', $test->id)" :title="$test->fullname" class="text-decoration-none" />
@else
    <span class="text-muted">
        N/A
    </span>
@endif
