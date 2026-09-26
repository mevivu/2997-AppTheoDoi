@if($icon)
    <img src="{{ asset($icon) }}" alt="{{ $name }}" class="rounded" style="width: 36px; height: 36px; object-fit: cover;" />
@else
    <span class="text-muted">—</span>
@endif
