@php
    $firstVid = isset($videos) ? (is_array($videos) ? ($videos[0] ?? null) : $videos->first()) : null;
    $vType = is_array($firstVid) ? ($firstVid['video_type'] ?? 'youtube') : ($firstVid?->video_type?->value ?? ($firstVid?->video_type ?? 'youtube'));
    $duration = is_array($firstVid) ? ($firstVid['formatted_duration'] ?? '00:00') : ($firstVid?->formatted_duration ?? '00:00');
    $isR2 = $vType === 'r2';
@endphp
@if($firstVid)
    <div class="d-flex flex-column align-items-center gap-1">
        <span class="badge {{ $isR2 ? 'bg-warning-lt text-warning' : 'bg-red-lt text-danger' }} px-2 py-1 fs-11">
            <i class="{{ $isR2 ? 'ti ti-cloud' : 'ti ti-brand-youtube' }} me-1"></i>{{ $isR2 ? 'R2' : 'YouTube' }}
        </span>
        <span class="text-muted fs-11"><i class="ti ti-clock me-1"></i>{{ $duration }}</span>
    </div>
@else
    <span class="text-muted fs-11">—</span>
@endif
