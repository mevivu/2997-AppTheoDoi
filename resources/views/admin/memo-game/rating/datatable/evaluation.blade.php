@php
    $badgeClass = match($evaluation_label) {
        'Xuất sắc' => 'bg-success text-white',
        'Tốt' => 'bg-primary text-white',
        'Khá' => 'bg-info text-white',
        'Trung bình' => 'bg-warning text-dark',
        default => 'bg-secondary text-white',
    };
@endphp

<span class="badge {{ $badgeClass }} px-2 py-1 fs-12">
    {{ $evaluation_label ?? __('Chưa xếp loại') }}
</span>
