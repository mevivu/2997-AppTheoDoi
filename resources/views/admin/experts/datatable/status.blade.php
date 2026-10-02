@php
    $statusValue = $status instanceof \App\Enums\DefaultStatus ? $status->value : $status;
@endphp
@if($statusValue == 1)
    <span class="badge bg-green-lt">{{ __('Hoạt động') }}</span>
@else
    <span class="badge bg-red-lt">{{ __('Tạm ẩn') }}</span>
@endif
