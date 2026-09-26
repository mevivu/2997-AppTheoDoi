@php
    $count = $videos_count ?? ($row->videos_count ?? 0);
@endphp

@if($count > 0)
    <span class="badge bg-blue-lt fw-bold px-2 py-1">
        <i class="ti ti-video me-1"></i> {{ $count }} video
    </span>
@else
    <span class="badge bg-secondary-lt px-2 py-1 text-muted">
        0 video
    </span>
@endif
