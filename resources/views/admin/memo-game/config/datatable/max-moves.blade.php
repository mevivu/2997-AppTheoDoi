@php
    $maxMoves = (int) ($max_moves ?? 0);
@endphp

@if ($maxMoves > 0)
    <span class="badge bg-warning-lt text-warning-emphasis px-2.5 py-1.5 fs-12 fw-semibold rounded-pill d-inline-flex align-items-center"
          title="{{ __('Game Over nếu dùng hết :count lượt mở (cả đúng và sai)', ['count' => $maxMoves]) }}">
        <i class="ti ti-hand-click me-1 fs-13"></i> {{ __('Tối đa') }} {{ $maxMoves }} {{ __('lượt') }}
    </span>
@else
    <span class="badge bg-secondary-lt text-secondary px-2.5 py-1.5 fs-12 fw-semibold rounded-pill d-inline-flex align-items-center"
          title="{{ __('Không giới hạn lượt mở, bé chơi đến khi xong hoặc hết giờ') }}">
        <i class="ti ti-infinity me-1 fs-13"></i> {{ __('Không giới hạn') }}
    </span>
@endif
