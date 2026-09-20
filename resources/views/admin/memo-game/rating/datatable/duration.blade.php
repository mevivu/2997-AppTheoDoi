@php
    $duration = (int) ($total_duration_spent ?? 0);
    $m = floor($duration / 60);
    $s = $duration % 60;
@endphp
<span class="badge bg-light text-dark border px-2 py-1 fs-12">
    <i class="ti ti-clock me-1 text-primary"></i>
    {{ $m > 0 ? $m . 'm ' : '' }}{{ $s . 's' }}
</span>
