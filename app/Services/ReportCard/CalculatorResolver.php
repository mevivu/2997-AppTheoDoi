<?php

namespace App\Services\ReportCard;

use App\Enums\Class\EducationLevel;
use App\Services\ReportCard\Contracts\ReportCardCalculator;
use InvalidArgumentException;

class CalculatorResolver
{
    public function __construct(
        protected ?PrimaryCalculator $primaryCalculator = null,
        protected ?SecondaryCalculator $secondaryCalculator = null,
    ) {
        $this->primaryCalculator = $primaryCalculator ?? new PrimaryCalculator();
        $this->secondaryCalculator = $secondaryCalculator ?? new SecondaryCalculator();
    }

    public function resolve(?string $educationLevel, ?int $classId = null): ReportCardCalculator
    {
        $level = $educationLevel;

        if (empty($level) && $classId !== null) {
            $level = EducationLevel::fromClassId($classId)->value;
        }

        return match ($level) {
            EducationLevel::Primary->value => $this->primaryCalculator,
            EducationLevel::LowerSecondary->value,
            EducationLevel::UpperSecondary->value => $this->secondaryCalculator,
            default => throw new InvalidArgumentException("Không thể xác định engine tính cho cấp học: '{$level}' (classId: {$classId})"),
        };
    }
}
