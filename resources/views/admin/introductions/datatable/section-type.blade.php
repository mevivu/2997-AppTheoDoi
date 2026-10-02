@php
    $typeEnum = $section_type instanceof \App\Enums\Introduction\IntroductionSectionType
        ? $section_type
        : \App\Enums\Introduction\IntroductionSectionType::tryFrom($section_type);
@endphp
@if($typeEnum)
    <span class="badge {{ $typeEnum->badge() }}">{{ $typeEnum->label() }}</span>
@else
    <span class="badge bg-secondary-lt">—</span>
@endif
