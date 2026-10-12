@php
    $stage = $milestone->stage;
    $badgeStyle = match($stage) {
        \App\Enums\Journal\MilestoneStage::Newborn => ['class' => 'bg-azure-lt text-azure border border-azure-subtle', 'icon' => 'ti-baby-bottle'],
        \App\Enums\Journal\MilestoneStage::Toddler => ['class' => 'bg-green-lt text-green border border-green-subtle', 'icon' => 'ti-walk'],
        \App\Enums\Journal\MilestoneStage::Preschool => ['class' => 'bg-purple-lt text-purple border border-purple-subtle', 'icon' => 'ti-palette'],
    };
@endphp
<span class="badge {{ $badgeStyle['class'] }} px-2.5 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 rounded-pill shadow-xs">
    <i class="ti {{ $badgeStyle['icon'] }} fs-14"></i>
    <span>{{ $stage->label() }}</span>
</span>

