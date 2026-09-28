@php
    $thumb = !empty($image) ? asset($image) : ($first_video_thumbnail ?? null);
@endphp
@if($thumb)
    <img src="{{ $thumb }}" alt="{{ $name }}" class="rounded shadow-sm" style="width: 56px; height: 36px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('assets/images/default.png') }}';">
@else
    <span class="avatar avatar-sm rounded bg-light text-muted">
        <i class="ti ti-book fs-3"></i>
    </span>
@endif
