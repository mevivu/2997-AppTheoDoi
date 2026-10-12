<?php

namespace App\Api\V1\Services\Journal;

use App\Api\V1\Exception\BadRequestException;
use App\Api\V1\Exception\NotFoundException;
use App\Api\V1\Repositories\Journal\JournalMilestoneRepositoryInterface;
use App\Enums\Child\BornStatus;
use App\Enums\Journal\JournalType;
use App\Enums\Journal\MilestoneStage;
use App\Enums\Permission\PermissionType;
use App\Models\Child;
use App\Models\Journal;
use App\Models\JournalMilestone;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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

        // 1. Lấy tất cả cột mốc đang hoạt động cho bé này (bao gồm mốc chuẩn của hệ thống + mốc riêng của bé)
        $milestones = JournalMilestone::query()
            ->forChild($childId)
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

        // Nhóm theo tất cả các giai đoạn phát triển
        $stagesConfig = [];
        foreach (MilestoneStage::cases() as $stageCase) {
            $stagesConfig[$stageCase->value] = [
                'key' => $stageCase->value,
                'label' => $stageCase->label(),
                'short_label' => $stageCase->shortLabel(),
                'icon' => $stageCase->icon(),
                'color' => $stageCase->color(),
                'milestones' => [],
                'achieved_count' => 0,
                'total' => 0,
            ];
        }

        foreach ($milestones as $milestone) {
            $stageKey = $milestone->stage instanceof \BackedEnum ? $milestone->stage->value : (string)$milestone->stage;
            if (!isset($stagesConfig[$stageKey])) {
                continue;
            }

            $isAchieved = isset($achievedJournals[$milestone->id]);
            $journal = $isAchieved ? $achievedJournals[$milestone->id] : null;

            $isCustom = ($milestone->type === PermissionType::USER || $milestone->type?->value === PermissionType::USER->value);
            $canDelete = $isCustom && ($milestone->user_id == $userId);

            $item = [
                'id' => $milestone->id,
                'name' => $milestone->name,
                'slug' => $milestone->slug,
                'icon' => $milestone->icon,
                'age_from_month' => $milestone->age_from_month,
                'age_to_month' => $milestone->age_to_month,
                'position' => $milestone->position,
                'type' => $milestone->type instanceof \BackedEnum ? $milestone->type->value : (string)($milestone->type ?? 'admin'),
                'is_custom' => $isCustom,
                'can_delete' => $canDelete,
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

    public function storeUserMilestone(Request $request): JournalMilestone
    {
        $userId = auth('api')->id() ?? auth()->id();
        $childId = (int)$request->input('child_id');

        $child = Child::where('id', $childId)
            ->where('user_id', $userId)
            ->first();

        if (!$child) {
            throw new NotFoundException('Trẻ không tồn tại hoặc không thuộc quyền quản lý của bạn.');
        }

        $stage = $request->input('stage');
        $stageVal = $stage instanceof MilestoneStage ? $stage->value : (string)$stage;

        $ageFrom = $request->input('age_from_month');
        $ageTo = $request->input('age_to_month');
        if ($ageFrom === null || $ageTo === null) {
            match ($stageVal) {
                MilestoneStage::Newborn->value => [$ageFrom ??= 0, $ageTo ??= 12],
                MilestoneStage::Toddler->value => [$ageFrom ??= 12, $ageTo ??= 36],
                MilestoneStage::Preschool->value => [$ageFrom ??= 36, $ageTo ??= 72],
                MilestoneStage::PrimarySchool->value => [$ageFrom ??= 72, $ageTo ??= 132],
                MilestoneStage::SecondarySchool->value => [$ageFrom ??= 132, $ageTo ??= 216],
                default => [$ageFrom ??= 0, $ageTo ??= 216],
            };
        }

        $name = trim(strip_tags((string)$request->input('name')));
        $slugBase = 'user-' . $childId . '-' . Str::slug($name);
        if (empty($slugBase) || $slugBase === "user-{$childId}-") {
            $slugBase = "user-{$childId}-milestone-" . time();
        }

        $slug = $slugBase;
        $counter = 1;
        while (JournalMilestone::where('slug', $slug)->exists()) {
            $slug = $slugBase . '-' . $counter++;
        }

        $maxPosition = JournalMilestone::where('stage', $stageVal)
            ->where(function ($q) use ($childId) {
                $q->where('type', PermissionType::ADMIN)
                    ->orWhere('child_id', $childId);
            })
            ->max('position') ?? 0;

        $position = $request->filled('position') ? (int)$request->input('position') : ($maxPosition + 1);

        return JournalMilestone::create([
            'stage' => $stageVal,
            'type' => PermissionType::USER,
            'child_id' => $childId,
            'user_id' => $userId,
            'name' => $name,
            'slug' => $slug,
            'icon' => $request->input('icon', '🚩'),
            'age_from_month' => (int)$ageFrom,
            'age_to_month' => (int)$ageTo,
            'position' => $position,
            'is_active' => true,
        ]);
    }

    public function updateUserMilestone(int $id, Request $request): JournalMilestone
    {
        $userId = auth('api')->id() ?? auth()->id();

        $milestone = JournalMilestone::find($id);
        if (!$milestone) {
            throw new NotFoundException('Cột mốc không tồn tại.');
        }

        if ($milestone->type !== PermissionType::USER && $milestone->type?->value !== PermissionType::USER->value) {
            throw new BadRequestException('Không thể chỉnh sửa cột mốc chuẩn của hệ thống.');
        }

        if ($milestone->user_id != $userId) {
            throw new BadRequestException('Bạn không có quyền chỉnh sửa cột mốc này.');
        }

        $data = [];
        if ($request->filled('name')) {
            $data['name'] = trim(strip_tags((string)$request->input('name')));
        }
        if ($request->has('icon')) {
            $data['icon'] = $request->input('icon');
        }
        if ($request->has('stage')) {
            $stage = $request->input('stage');
            $data['stage'] = $stage instanceof MilestoneStage ? $stage->value : (string)$stage;
        }
        if ($request->has('age_from_month')) {
            $data['age_from_month'] = (int)$request->input('age_from_month');
        }
        if ($request->has('age_to_month')) {
            $data['age_to_month'] = (int)$request->input('age_to_month');
        }
        if ($request->has('position')) {
            $data['position'] = (int)$request->input('position');
        }
        if ($request->has('is_active')) {
            $data['is_active'] = (bool)$request->input('is_active');
        }

        $milestone->update($data);

        return $milestone->fresh();
    }

    public function deleteUserMilestone(int $id): bool
    {
        $userId = auth('api')->id() ?? auth()->id();

        $milestone = JournalMilestone::find($id);
        if (!$milestone) {
            throw new NotFoundException('Cột mốc không tồn tại.');
        }

        if ($milestone->type !== PermissionType::USER && $milestone->type?->value !== PermissionType::USER->value) {
            throw new BadRequestException('Không thể xoá cột mốc chuẩn của hệ thống.');
        }

        if ($milestone->user_id != $userId) {
            throw new BadRequestException('Bạn không có quyền xoá cột mốc này.');
        }

        // Khi xoá mốc tự tạo: set milestone_id = null cho các nhật ký đã gắn mốc này
        Journal::where('milestone_id', $milestone->id)->update(['milestone_id' => null]);

        return (bool)$milestone->delete();
    }
}

