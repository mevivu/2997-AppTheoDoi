<?php

namespace App\Api\V1\Services\Journal;

use App\Api\V1\Exception\NotFoundException;
use App\Api\V1\Repositories\Journal\JournalMilestoneRepositoryInterface;
use App\Enums\Child\BornStatus;
use App\Enums\Journal\JournalType;
use App\Enums\Journal\MilestoneStage;
use App\Models\Child;
use App\Models\Journal;
use App\Models\JournalMilestone;
use Carbon\Carbon;
use Illuminate\Http\Request;

class JournalMilestoneService implements JournalMilestoneServiceInterface
{
    protected JournalMilestoneRepositoryInterface $repository;

    public function __construct(JournalMilestoneRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getMilestonesForChild(Request $request): array
    {
        $childId = (int)$request->input('child_id');
        $userId = auth('api')->id() ?? auth()->id();

        $child = Child::where('id', $childId)
            ->where('user_id', $userId)
            ->first();

        if (!$child) {
            throw new NotFoundException('Trẻ không tồn tại hoặc không thuộc quyền quản lý của bạn.');
        }

        // Tính giai đoạn đề xuất
        $suggestedStage = MilestoneStage::Newborn;
        if ($child->birthday && $child->is_born !== BornStatus::Unborn) {
            $ageInMonths = (int)Carbon::parse($child->birthday)->diffInMonths(Carbon::now());
            $suggestedStage = MilestoneStage::fromAgeMonths($ageInMonths);
        }

        // 1. Lấy tất cả cột mốc đang hoạt động
        $milestones = JournalMilestone::query()
            ->where('is_active', true)
            ->orderBy('stage')
            ->orderBy('position')
            ->orderBy('id')
            ->get();

        // 2. Lấy danh sách các cột mốc bé đã đạt được (type = moment)
        $achievedJournals = Journal::query()
            ->where('child_id', $childId)
            ->where('type', JournalType::Moment->value)
            ->whereNotNull('milestone_id')
            ->get()
            ->keyBy('milestone_id');

        // Nhóm theo các giai đoạn
        $stagesConfig = [
            MilestoneStage::Newborn->value => [
                'key' => MilestoneStage::Newborn->value,
                'label' => MilestoneStage::Newborn->label(),
                'milestones' => [],
                'achieved_count' => 0,
                'total' => 0,
            ],
            MilestoneStage::Toddler->value => [
                'key' => MilestoneStage::Toddler->value,
                'label' => MilestoneStage::Toddler->label(),
                'milestones' => [],
                'achieved_count' => 0,
                'total' => 0,
            ],
            MilestoneStage::Preschool->value => [
                'key' => MilestoneStage::Preschool->value,
                'label' => MilestoneStage::Preschool->label(),
                'milestones' => [],
                'achieved_count' => 0,
                'total' => 0,
            ],
        ];

        foreach ($milestones as $milestone) {
            $stageKey = $milestone->stage instanceof \BackedEnum ? $milestone->stage->value : (string)$milestone->stage;
            if (!isset($stagesConfig[$stageKey])) {
                continue;
            }

            $isAchieved = isset($achievedJournals[$milestone->id]);
            $journal = $isAchieved ? $achievedJournals[$milestone->id] : null;

            $item = [
                'id' => $milestone->id,
                'name' => $milestone->name,
                'slug' => $milestone->slug,
                'icon' => $milestone->icon,
                'age_from_month' => $milestone->age_from_month,
                'age_to_month' => $milestone->age_to_month,
                'position' => $milestone->position,
                'is_achieved' => $isAchieved,
                'journal_id' => $journal?->id,
                'achieved_at' => $journal?->created_at ? format_date($journal->created_at) : null,
            ];

            $stagesConfig[$stageKey]['milestones'][] = $item;
            $stagesConfig[$stageKey]['total']++;
            if ($isAchieved) {
                $stagesConfig[$stageKey]['achieved_count']++;
            }
        }

        return [
            'suggested_stage' => $suggestedStage->value,
            'suggested_stage_label' => $suggestedStage->label(),
            'stages' => array_values($stagesConfig),
        ];
    }
}
