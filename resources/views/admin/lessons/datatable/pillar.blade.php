@php
    $pillarVal = is_array($category ?? null) ? ($category['pillar'] ?? null) : ($category?->pillar ?? null);
    $pillarEnum = $pillarVal instanceof \App\Enums\Lesson\EducationPillar 
        ? $pillarVal 
        : (is_string($pillarVal) ? \App\Enums\Lesson\EducationPillar::tryFrom($pillarVal) : null);
@endphp
@if($pillarEnum)
    <span class="badge {{ $pillarEnum->badge() }} d-inline-flex align-items-center gap-1 px-2 py-1">
        <i class="{{ $pillarEnum->icon() }}"></i>
        <span>{{ $pillarEnum->label() }}</span>
    </span>
@else
    <span class="text-muted">—</span>
@endif
