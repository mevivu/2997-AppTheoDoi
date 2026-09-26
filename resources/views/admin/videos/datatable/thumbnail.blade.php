<div class="position-relative d-inline-block">
    <img src="{{ $thumbnail_url }}" alt="{{ $title }}" class="rounded shadow-sm" style="width: 70px; height: 45px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('assets/images/default.png') }}';" />
    @if($video_url)
        <a href="{{ $video_url }}" target="_blank" class="position-absolute top-50 start-50 translate-middle text-white" style="text-shadow: 0 0 4px rgba(0,0,0,0.8);" title="{{ __('Xem trên YouTube') }}">
            <i class="ti ti-brand-youtube fs-18 text-danger"></i>
        </a>
    @endif
</div>
