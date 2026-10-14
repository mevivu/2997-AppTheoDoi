<?php

namespace App\Admin\Services\IqNorm;

use App\Admin\Repositories\IqNorm\IqNormRepositoryInterface;
use App\Enums\ActiveStatus;
use App\Enums\Question\QuestionType;
use App\Models\IqNorm;
use App\Models\Rating;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class IqNormService implements IqNormServiceInterface
{
    protected IqNormRepositoryInterface $repository;

    public function __construct(IqNormRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function store(Request $request)
    {
        $data = $request->validated();
        $record = $this->repository->create($data);
        Cache::forget('iq_norms_all_active');
        return $record;
    }

    public function update(Request $request)
    {
        $data = $request->validated();
        $record = $this->repository->update($data['id'], $data);
        Cache::forget('iq_norms_all_active');
        return $record;
    }

    public function actionMultipleRecords(Request $request): bool
    {
        $data = $request->all();

        switch ($data['action']) {
            case ActiveStatus::Active->value:
                foreach ($data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', ActiveStatus::Active);
                }
                Cache::forget('iq_norms_all_active');
                return true;
            case ActiveStatus::Draft->value:
                foreach ($data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', ActiveStatus::Draft);
                }
                Cache::forget('iq_norms_all_active');
                return true;
            case ActiveStatus::Deleted->value:
                foreach ($data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', ActiveStatus::Deleted);
                }
                Cache::forget('iq_norms_all_active');
                return true;

            default:
                return false;
        }
    }

    /**
     * Tự động quét lại toàn bộ dữ liệu bảng ratings để tái chuẩn hóa (Recalibrate) Mean và StdDev
     */
    public function recalculateFromRatings(): array
    {
        $ratings = Rating::where('type', QuestionType::IQ)
            ->whereNotNull('score')
            ->where('score', '>', 0)
            ->with('child')
            ->get();

        $scoresByAge = [];

        foreach ($ratings as $r) {
            // Xác định độ tuổi
            $age = null;
            if ($r->child && $r->child->birthday) {
                $evalDate = $r->created_at ?? now();
                $age = (int) floor(Carbon::parse($r->child->birthday)->diffInDays(Carbon::parse($evalDate)) / 365.3);
            }
            if (!$age || $age <= 0) {
                $age = (int) ($r->age ?: ($r->child?->age ?: 0));
            }

            if ($age < 1 || $age > 20) {
                continue;
            }

            // Trích xuất điểm thô: Ưu tiên lấy từ result (vd '90/150' -> 90) hoặc score * 15
            $rawScore = null;
            if (!empty($r->result) && str_contains($r->result, '/')) {
                $parts = explode('/', $r->result);
                $rawScore = (float) trim($parts[0]);
            }
            if ($rawScore === null || $rawScore <= 0) {
                $rawScore = (float) $r->score * 15;
            }

            $scoresByAge[$age][] = $rawScore;
        }

        // Nếu nhóm 15 và 16 tuổi mẫu nhỏ, có thể gộp để tính ổn định
        $results = [];
        $totalAgesUpdated = 0;

        foreach ($scoresByAge as $age => $scores) {
            $count = count($scores);
            if ($count < 2) {
                continue;
            }

            $mean = array_sum($scores) / $count;
            $variance = 0.0;
            foreach ($scores as $s) {
                $variance += pow($s - $mean, 2);
            }
            $sd = sqrt($variance / ($count - 1));
            $min = min($scores);
            $max = max($scores);

            IqNorm::updateOrCreate(
                ['age' => $age],
                [
                    'mean_score' => round($mean, 2),
                    'sd_score' => round($sd, 2),
                    'sample_size' => $count,
                    'min_raw_score' => $min,
                    'max_raw_score' => $max,
                    'version' => 'auto_recalculated_' . now()->format('Ymd'),
                    'status' => ActiveStatus::Active,
                    'note' => "Được tái chuẩn hóa tự động từ {$count} bản ghi thực tế lúc " . now()->format('d/m/Y H:i'),
                ]
            );

            $results[$age] = [
                'count' => $count,
                'mean' => round($mean, 2),
                'sd' => round($sd, 2),
            ];
            $totalAgesUpdated++;
        }

        Cache::forget('iq_norms_all_active');

        return [
            'total_ratings_processed' => $ratings->count(),
            'total_ages_updated' => $totalAgesUpdated,
            'details' => $results,
        ];
    }
}
