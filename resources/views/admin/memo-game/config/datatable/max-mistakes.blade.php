@php
    $maxMistakes = (int) ($max_mistakes ?? 0);
@endphp

@if ($maxMistakes > 0)
    <span class="badge bg-danger-lt text-danger px-2.5 py-1.5 fs-12 fw-semibold rounded-pill d-inline-flex align-items-center"
          title="{{ __('Game Over nếu lật sai từ :count lần trở lên', ['count' => $maxMistakes]) }}">
        <i class="ti ti-alert-triangle me-1 fs-13"></i> {{ __('Tối đa') }} {{ $maxMistakes }} {{ __('lần') }}
    </span>
@else
    <span class="badge bg-secondary-lt text-secondary px-2.5 py-1.5 fs-12 fw-semibold rounded-pill d-inline-flex align-items-center"
          title="{{ __('Không giới hạn số lần sai, bé chơi đến khi xong hoặc hết giờ') }}">
        <i class="ti ti-infinity me-1 fs-13"></i> {{ __('Không giới hạn') }}
    </span>
@endif
