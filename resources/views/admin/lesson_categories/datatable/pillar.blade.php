@php
    $pillarEnum = is_string($pillar) ? \App\Enums\Lesson\EducationPillar::tryFrom($pillar) : $pillar;
@endphp
@if($pillarEnum)
    <span class="badge {{ $pillarEnum->badge() }} d-inline-flex align-items-center gap-1 px-2 py-1">
        <i class="{{ $pillarEnum->icon() }}"></i>
        <span>{{ $pillarEnum->label() }}</span>
    </span>
@else
    <span class="badge bg-secondary-lt">{{ $pillar }}</span>
@endif
