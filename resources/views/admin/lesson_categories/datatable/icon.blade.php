@if(!empty($icon))
    <img src="{{ asset($icon) }}" alt="{{ $name }}" class="rounded shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">
@else
    <span class="avatar avatar-sm rounded bg-light text-muted">
        <i class="ti ti-folder fs-3"></i>
    </span>
@endif
