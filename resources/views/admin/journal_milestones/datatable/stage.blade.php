@php
    $stage = $milestone->stage;
    $color = $stage instanceof \App\Enums\Journal\MilestoneStage ? $stage->color() : 'primary';
    $icon = $stage instanceof \App\Enums\Journal\MilestoneStage ? $stage->icon() : 'ti-flag';
    $label = $stage instanceof \App\Enums\Journal\MilestoneStage ? $stage->label() : (string)$stage;
@endphp
<span class="badge bg-{{ $color }}-lt text-{{ $color }} border border-{{ $color }}-subtle px-2.5 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 rounded-pill shadow-xs">
    <i class="ti {{ $icon }} fs-14"></i>
    <span>{{ $label }}</span>
</span>


