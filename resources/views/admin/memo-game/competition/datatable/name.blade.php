@php
    use App\Traits\RouteAdminSystem;
    $rows = $age_config['rows'] ?? ($ageConfig->rows ?? 5);
    $cols = $age_config['columns'] ?? ($ageConfig->columns ?? 6);
    $totalCards = $age_config['total_cards'] ?? ($ageConfig->total_cards ?? 30);
@endphp
<div class="text-start">
    <a href="{{ route(RouteAdminSystem::MEMO_COMPETITION_EDIT, $id) }}" class="fw-bold text-dark fs-13 text-decoration-none hover-primary d-block mb-1">
        {{ $name }}
    </a>
    <div class="d-flex align-items-center gap-1 flex-wrap">
        <span class="badge bg-primary-lt fs-10">
            <i class="ti ti-grid-dots me-1"></i>{{ "{$rows}×{$cols} ({$totalCards} thẻ)" }}
        </span>
        <span class="badge bg-secondary-lt fs-10">
            <i class="ti ti-eye-off me-1"></i>{{ __('0s xem trước') }}
        </span>
        @if ($max_attempts == 1)
            <span class="badge bg-success-lt fs-10">{{ __('1 lượt duy nhất') }}</span>
        @elseif ($max_attempts > 1)
            <span class="badge bg-info-lt fs-10">{{ __('Tối đa') }} {{ $max_attempts }} {{ __('lượt') }}</span>
        @else
            <span class="badge bg-light text-muted fs-10">{{ __('Không giới hạn') }}</span>
        @endif
    </div>
</div>
