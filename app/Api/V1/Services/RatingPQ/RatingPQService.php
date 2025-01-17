<?php

namespace App\Api\V1\Services\RatingPQ;


use App\Admin\Services\File\FileService;
use App\Api\V1\Repositories\BMI\BMIRepositoryInterface;
use App\Api\V1\Repositories\Child\ChildRepositoryInterface;
use App\Api\V1\Repositories\RatingPQ\RatingPQRepositoryInterface;
use App\Api\V1\Repositories\WeightHeightWho\WhoRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\AuthSupport;
use App\Enums\ActiveStatus;
use App\Enums\Child\BornStatus;
use Exception;
use Illuminate\Http\Request;


class RatingPQService implements RatingPQServiceInterface
{
    use AuthSupport, AuthServiceApi;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;

    protected RatingPQRepositoryInterface $repository;
    protected ChildRepositoryInterface $childRepository;
    protected BMIRepositoryInterface $bmiRepository;

    protected WhoRepositoryInterface $whoRepository;
    protected FileService $fileService;

    public function __construct(
        RatingPQRepositoryInterface $repository,
        ChildRepositoryInterface    $childRepository,
        BMIRepositoryInterface      $bmiRepository,
        WhoRepositoryInterface      $whoRepository,
        FileService                 $fileService
    )
    {
        $this->repository = $repository;
        $this->childRepository = $childRepository;
        $this->bmiRepository = $bmiRepository;
        $this->whoRepository = $whoRepository;
        $this->fileService = $fileService;
    }


    public function index(Request $request)
    {
        $data = $request->validated();
        $limit = $data['limit'] ?? 10;
        $page = $data['page'] ?? 1;
        $type = $data['type'];

        $query = $this->repository->getByQueryBuilder([
            'child_id' => $data['child_id'],
            'type' => $type,
        ]);
        return $query->paginate($limit, ['*'], 'page', $page);
    }

    /**
     * @throws Exception
     */
    public function store(Request $request): object
    {
        $data = $request->validated();
        $height = $data['height'];
        $weight = $data['weight'];
        $child = $this->childRepository->findOrFail($data['child_id']);
        $bmi = $this->calculateBMI($height, $weight);
        $age = $child->age;
        $gender = $child->gender;
        $month = $child->month;
        $bmiCategory = $this->getBmiCategory($bmi, $age, $gender);
        $data['bmi'] = $bmi;
        $data['bmi_result'] = $bmiCategory;
        $data['height_result'] = $this->getHeightResult($height, $month, $gender);
        return $this->repository->create($data);
    }

    public function getHeightResult($currentHeight, $month, $gender): string
    {
        $who = $this->whoRepository->getBy(
            [
                'month' => $month,
                'gender' => $gender,
                'status' => ActiveStatus::Active,
            ]
        )->first();
        if (!$who) {
            return 'Dữ liệu không xác định';
        }
        $heightWho = $who->height;
        $heightChangeWho = $who->height_change;
        $baseLow = ($heightWho - $heightChangeWho);
        $baseHigh = ($heightWho + $heightChangeWho);
        $veryLow = $baseLow * 12;
        $low = $baseLow * 6;
        $slightlyLow = $baseLow * 3;
        $normal = $baseLow;
        $slightlyHigh = $baseHigh * 3;
        $high = $baseHigh * 6;
        $veryHigh = $baseHigh * 12;
        if ($currentHeight <= $veryLow) {
            return 'Rất thấp';
        } elseif ($currentHeight > $veryLow && $currentHeight <= $low) {
            return 'Tương đối thấp';
        } elseif ($currentHeight > $low && $currentHeight <= $slightlyLow) {
            return 'Hơi thấp';
        } elseif ($currentHeight > $slightlyLow && $currentHeight <= $normal) {
            return 'Bình thường';
        } elseif ($currentHeight > $normal && $currentHeight <= $slightlyHigh) {
            return 'Vượt chuẩn';
        } elseif ($currentHeight > $slightlyHigh && $currentHeight <= $high) {
            return 'Tương đối cao';
        } elseif ($currentHeight > $high) {
            return 'Rất cao';
        }
        return 'Không xác định';
    }

    public function getBmiCategory($bmi, $age, $gender): ?string
    {
        $bmiCategory = null;
        if ($age) {
            $bmiThresholds = $this->bmiRepository->getBy([
                'age' => $age,
                'gender' => $gender,
                'status' => ActiveStatus::Active
            ])->first();
            if ($bmiThresholds) {
                $bmiCategory = $this->classifyBMI($bmi, $bmiThresholds);
            }
        }
        return $bmiCategory;
    }

    public function calculateBMI($height, $weight): float|int
    {
        if ($height <= 0) return 0;

        $heightInMeters = $height / 100;
        $bmi = $weight / ($heightInMeters * $heightInMeters);

        return round($bmi, 1);
    }

    public function classifyBMI($bmi, $bmiThresholds): string
    {
        if (!$bmiThresholds) return 'Không thể xác định';

        $conditions = [
            'Suy dinh dưỡng' => function ($bmi) use ($bmiThresholds) {
                return $bmi <= $bmiThresholds->z_score_minus_3;
            },
            'Quá gầy' => function ($bmi) use ($bmiThresholds) {
                return $bmi <= $bmiThresholds->z_score_minus_2;
            },
            'Hơi gầy' => function ($bmi) use ($bmiThresholds) {
                return $bmi <= $bmiThresholds->z_score_minus_1;
            },
            'Bình thường' => function ($bmi) use ($bmiThresholds) {
                return $bmi <= $bmiThresholds->z_score_0;
            },
            'Hơi béo' => function ($bmi) use ($bmiThresholds) {
                return $bmi <= $bmiThresholds->z_score_plus_1;
            },
            'Tương đối béo' => function ($bmi) use ($bmiThresholds) {
                return $bmi <= $bmiThresholds->z_score_plus_2;
            },
            'Béo phì' => function ($bmi) {
                return true;
            },
        ];

        foreach ($conditions as $result => $condition) {
            if ($condition($bmi)) {
                return $result;
            }
        }

        return 'Không thể xác định';
    }


    /**
     * @throws Exception
     */
    public function delete($id): void
    {
        $this->repository->delete($id);

    }


}
