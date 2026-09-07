@php
    $count = (int)($max_devices ?? 1);
@endphp
<div class="text-center">
    @if($count === 1)
        <span class="badge bg-blue-lt py-1 px-2 fs-6" title="Gói cơ bản: 01 thiết bị">
            <i class="ti ti-device-mobile me-1"></i> 1 thiết bị
        </span>
    @elseif($count <= 3)
        <span class="badge bg-teal-lt py-1 px-2 fs-6" title="Gói tiêu chuẩn: {{ $count }} thiết bị">
            <i class="ti ti-devices me-1"></i> {{ $count }} thiết bị
        </span>
    @elseif($count <= 5)
        <span class="badge bg-purple-lt py-1 px-2 fs-6 fw-bold" title="Gói VIP: {{ $count }} thiết bị">
            <i class="ti ti-devices-pc me-1"></i> {{ $count }} thiết bị
        </span>
    @else
        <span class="badge bg-orange-lt py-1 px-2 fs-6 fw-bold" title="Gói mở rộng: {{ $count >= 999 ? 'Không giới hạn' : $count . ' thiết bị' }}">
            <i class="ti ti-infinity me-1"></i> {{ $count >= 999 ? 'Không giới hạn' : $count . ' thiết bị' }}
        </span>
    @endif
</div>
