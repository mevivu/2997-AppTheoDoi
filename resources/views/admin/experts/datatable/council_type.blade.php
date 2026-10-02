@php
    $type = $council_type instanceof \App\Enums\Expert\ExpertCouncilType
        ? $council_type
        : \App\Enums\Expert\ExpertCouncilType::tryFrom((int)$council_type);
@endphp

@if($type)
    <span class="badge {{ $type->badge() }}">
        {{ $type->label() }}
    </span>
@else
    <span class="badge bg-secondary-lt">
        {{ __('Chưa phân nhóm') }}
    </span>
@endif
