@php
    $ageGroupName = is_array($age_group ?? ($ageGroup ?? null))
        ? ($age_group['name'] ?? $ageGroup['name'] ?? null)
        : ($age_group->name ?? $ageGroup->name ?? null);
@endphp

@if(!empty($ageGroupName))
    <span class="badge bg-purple-lt">{{ $ageGroupName }}</span>
@else
    <span class="badge bg-secondary-lt">{{ __('Mọi lứa tuổi') }}</span>
@endif
