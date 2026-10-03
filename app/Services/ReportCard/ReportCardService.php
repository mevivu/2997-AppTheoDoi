<?php

namespace App\Services\ReportCard;

use App\Enums\Semester\SemesterStatus;
use App\Models\ClassGrade;
use Illuminate\Support\Facades\Log;
use Throwable;

class ReportCardService
{
    public function __construct(
        protected ?ReportCardInputLoader $loader = null,
        protected ?ReportCardEngine $engine = null,
        protected ?ReportCardPersister $persister = null
    ) {
        $this->loader = $loader ?? new ReportCardInputLoader();
        $this->engine = $engine ?? new ReportCardEngine();
        $this->persister = $persister ?? new ReportCardPersister();
    }

    /**
     * Tính toán và lưu kết quả cho cả 3 kỳ học của một ClassGrade
     */
    public function recalculateClassGrade(ClassGrade $classGrade, ?int $touchedEvaluationId = null): void
    {
        $semesters = [
            SemesterStatus::Semester1->value,
            SemesterStatus::Semester2->value,
            SemesterStatus::FullYear->value,
        ];

        foreach ($semesters as $semester) {
            try {
                $input = $this->loader->load($classGrade, $semester);
                $result = $this->engine->calculate($input);
                $this->persister->persist($classGrade, $semester, $result, $input);
            } catch (Throwable $e) {
                // Security / Logging rule: không ghi dữ liệu cá nhân hay token vào log
                Log::warning('ReportCard recalculation error', [
                    'class_grade_id' => $classGrade->id,
                    'semester' => $semester,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
