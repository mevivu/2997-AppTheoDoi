@php
    $statusValue = $status instanceof \App\Enums\DefaultStatus ? $status->value : $status;
@endphp
@if($statusValue == 1)
    <span class="badge bg-green-lt">{{ __('Xuất bản') }}</span>
@else
    <span class="badge bg-red-lt">{{ __('Bản nháp') }}</span>
@endif
