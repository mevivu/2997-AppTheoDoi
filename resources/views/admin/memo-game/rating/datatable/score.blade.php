@php
    $numScore = (float) ($score ?? 0);
    $formattedScore = number_format($numScore, 1);

    if ($numScore >= 90) {
        $style = 'background-color: #fffbeb !important; color: #92400e !important; border: 1px solid #fde68a !important;';
        $icon = 'ti-trophy';
        $iconColor = '#d97706';
    } elseif ($numScore >= 70) {
        $style = 'background-color: #eff6ff !important; color: #1e40af !important; border: 1px solid #bfdbfe !important;';
        $icon = 'ti-award';
        $iconColor = '#2563eb';
    } elseif ($numScore >= 50) {
        $style = 'background-color: #f0fdf4 !important; color: #166534 !important; border: 1px solid #bbf7d0 !important;';
        $icon = 'ti-star';
        $iconColor = '#16a34a';
    } elseif ($numScore > 0) {
        $style = 'background-color: #fff7ed !important; color: #c2410c !important; border: 1px solid #fed7aa !important;';
        $icon = 'ti-alert-triangle';
        $iconColor = '#ea580c';
    } else {
        $style = 'background-color: #f8fafc !important; color: #64748b !important; border: 1px solid #e2e8f0 !important;';
        $icon = 'ti-circle-minus';
        $iconColor = '#94a3b8';
    }
@endphp

<span class="badge rounded-pill px-2.5 py-1 fs-12 fw-bold font-monospace d-inline-flex align-items-center gap-1 shadow-sm"
      style="{{ $style }}">
    <i class="ti {{ $icon }}" style="color: {{ $iconColor }}; font-size: 13px;"></i>
    <span>{{ $formattedScore }}</span>
</span>

