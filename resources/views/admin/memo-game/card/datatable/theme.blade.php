@php
    $themeData = $theme ?? ($instance->theme ?? null);
    $name = is_array($themeData) ? ($themeData['name'] ?? '') : ($themeData->name ?? '');
    $code = strtolower(is_array($themeData) ? ($themeData['code'] ?? '') : ($themeData->code ?? ''));
    $badgeClass = 'badge-memo-theme-default';
    $icon = 'ti ti-cards';

    if (str_contains($code, 'vehic') || str_contains($code, 'xe')) {
        $badgeClass = 'badge-memo-theme-vehicles';
        $icon = 'ti ti-car';
    } elseif (str_contains($code, 'flow') || str_contains($code, 'hoa')) {
        $badgeClass = 'badge-memo-theme-flowers';
        $icon = 'ti ti-flower';
    } elseif (str_contains($code, 'numb') || str_contains($code, 'so')) {
        $badgeClass = 'badge-memo-theme-numbers';
        $icon = 'ti ti-numbers';
    } elseif (str_contains($code, 'flag') || str_contains($code, 'co')) {
        $badgeClass = 'badge-memo-theme-flags';
        $icon = 'ti ti-flag';
    }
@endphp

@if(!empty($name))
    <span class="badge {{ $badgeClass }} fs-12 px-2.5 py-1.5 rounded-pill d-inline-flex align-items-center gap-1">
        <i class="{{ $icon }}"></i>
        <span>{{ $name }}</span>
    </span>
@else
    <span class="text-muted fs-12">---</span>
@endif
