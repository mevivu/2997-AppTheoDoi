<?php

namespace App\Api\V1\Services\Pregnancy;


use App\Admin\Services\File\FileService;
use App\Api\V1\Repositories\Pregnancy\PregnancyRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\AuthSupport;
use App\Enums\ActiveStatus;
use App\Models\Child;
use App\Models\FetalGrowthStandard;
use App\Models\Pregnancy;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;


class PregnancyService implements PregnancyServiceInterface
{
    use AuthSupport, AuthServiceApi;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;

    protected PregnancyRepositoryInterface $repository;

    protected FileService $fileService;


    public function __construct(
        PregnancyRepositoryInterface $repository,
        FileService                  $fileService
    )
    {
        $this->repository = $repository;
        $this->fileService = $fileService;
    }


    public function index(Request $request)
    {
        $data = $request->validated();
        $limit = $data['limit'] ?? 10;
        $page = $data['page'] ?? 1;
        $query = $this->repository->getQueryBuilder();
        $query->where('child_id', $data['child_id'] ?? null);
        $query->orderBy('week', 'asc');


        return $query->paginate($limit, ['*'], 'page', $page);
    }

    /**
     * @throws Exception
     */
    public function store(Request $request): object
    {
        $data = $request->validated();
        $image = $data['image'] ?? null;
        if ($image) {
            $data['image'] = $this->fileService->uploadAvatar('images/pregnancy', $image);
        }
        $data['week'] = $data['week'] ?? null;
        $data['weight'] = $data['weight'] ?? null;
        $data['length'] = $data['length'] ?? null;
        $data['head_circumference'] = $data['head_circumference'] ?? null;
        return $this->repository->create($data);
    }

    /**
     * @throws Exception
     */
    public function update(Request $request): object
    {
        $data = $request->validated();
        $image = $data['image'] ?? null;
        $pregnancy = $this->repository->findOrFail($data['id']);
        if ($image) {
            $data['image'] = $this->fileService->uploadAvatar('images/pregnancy', $image, $pregnancy->image);
        }
        $pregnancy->update($data);

        return $pregnancy;
    }


    /**
     * @throws Exception
     */
    public function delete($id): void
    {
        $response = $this->repository->findOrFail($id);
        $this->fileService->deleteModelImages($response, ['image']);
        $this->repository->delete($id);
    }

    public function trackingOverview(Request $request): array
    {
        $childId = $request->input('child_id');
        $child = null;
        if ($childId) {
            $child = Child::find($childId);
        }

        if (!$child) {
            $user = auth('api')->user();
            if ($user) {
                $child = Child::where('user_id', $user->id)
                    ->where(function ($q) {
                        $q->where('is_born', 0)->orWhereNotNull('due_date');
                    })
                    ->first() ?? Child::where('user_id', $user->id)->first();
            }
        }

        $today = Carbon::now()->startOfDay();
        $dueDate = null;

        if ($child?->due_date) {
            $dueDate = Carbon::parse($child->due_date)->startOfDay();
        } elseif ($child?->birthday) {
            $dueDate = Carbon::parse($child->birthday)->startOfDay();
        } else {
            $dueDate = Carbon::now()->addDays(100)->startOfDay();
        }

        $daysRemaining = (int) $today->diffInDays($dueDate, false);
        $gestationalAgeDays = 280 - $daysRemaining;

        if ($gestationalAgeDays < 0) {
            $currentWeek = 0;
            $extraDays = 0;
        } else {
            $currentWeek = intdiv($gestationalAgeDays, 7);
            $extraDays = $gestationalAgeDays % 7;
        }

        $progressPercent = round(min(max(($gestationalAgeDays / 280) * 100, 0), 100), 1);

        $ageDisplay = "Tuần {$currentWeek}" . ($extraDays > 0 ? " + {$extraDays} ngày" : "");

        if ($daysRemaining > 0) {
            $daysRemainingDisplay = "Còn {$daysRemaining} ngày chào đời";
        } elseif ($daysRemaining === 0) {
            $daysRemainingDisplay = "Hôm nay là ngày dự sinh";
        } else {
            $daysRemainingDisplay = "Quá ngày dự sinh " . abs($daysRemaining) . " ngày";
        }

        $dueDateDisplay = "Dự sinh: " . $dueDate->format('d/m/Y');

        // Tiêu chuẩn tuần hiện tại từ bảng fetal_growth_standards
        $lookupWeek = max(1, min(50, $currentWeek > 0 ? $currentWeek : 8));
        $standardQuery = FetalGrowthStandard::where('status', ActiveStatus::Active->value);
        $currentStandard = (clone $standardQuery)->where('week', $lookupWeek)->first();
        if (!$currentStandard) {
            $currentStandard = (clone $standardQuery)->orderByRaw("ABS(week - {$lookupWeek}) ASC")->first();
        }

        $standardData = [
            'week' => (int) ($currentStandard?->week ?? $lookupWeek),
            'length' => (float) ($currentStandard?->length ?? 0),
            'length_display' => ($currentStandard?->length ? (float)$currentStandard->length : 0) . ' cm',
            'weight' => (float) ($currentStandard?->weight ?? 0),
            'weight_display' => ($currentStandard?->weight ? (float)$currentStandard->weight : 0) . ' g',
            'head_circumference' => $currentStandard?->head_circumference ? (float) $currentStandard->head_circumference : null,
            'description' => $currentStandard?->description ?? '',
        ];

        // Biểu đồ chuẩn tăng trưởng thai nhi từ CMS
        $growthChart = FetalGrowthStandard::where('status', ActiveStatus::Active->value)
            ->orderBy('week', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'week' => (int) $item->week,
                    'length' => (float) $item->length,
                    'weight' => (float) $item->weight,
                    'head_circumference' => $item->head_circumference ? (float) $item->head_circumference : null,
                ];
            });

        // Lịch sử nhập liệu của bé
        $userLogs = [];
        if ($child) {
            $userLogs = Pregnancy::where('child_id', $child->id)
                ->orderBy('week', 'asc')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => (int) $item->id,
                        'week' => (int) $item->week,
                        'weight' => $item->weight ? (float) $item->weight : null,
                        'length' => $item->length ? (float) $item->length : null,
                        'head_circumference' => $item->head_circumference ? (float) $item->head_circumference : null,
                        'image' => $item->image ? asset($item->image) : null,
                        'created_at' => $item->created_at?->format('d/m/Y'),
                    ];
                });
        }

        return [
            'child_id' => $child?->id,
            'child_name' => $child?->fullname,
            'due_date' => $dueDate->format('Y-m-d'),
            'due_date_display' => $dueDateDisplay,
            'days_remaining' => $daysRemaining,
            'days_remaining_display' => $daysRemainingDisplay,
            'gestational_age_days' => $gestationalAgeDays,
            'current_week' => $currentWeek,
            'extra_days' => $extraDays,
            'age_display' => $ageDisplay,
            'progress_percent' => $progressPercent,
            'standard' => $standardData,
            'growth_chart' => $growthChart,
            'user_logs' => $userLogs,
        ];
    }
}
