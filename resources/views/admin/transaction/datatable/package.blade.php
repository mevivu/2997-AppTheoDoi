@if ($package)
    <x-link target="_blank" :href="route('admin.package.edit', $package->id)" class="d-inline-flex align-items-center text-decoration-none">
        <span class="badge bg-indigo-subtle text-indigo border border-indigo-subtle px-2.5 py-1 fs-12 fw-semibold d-inline-flex align-items-center gap-1">
            <i class="ti ti-package fs-13"></i>
            <span>{{ $package->name }}</span>
        </span>
    </x-link>
@else
    <span class="text-muted fst-italic">N/A</span>
@endif
