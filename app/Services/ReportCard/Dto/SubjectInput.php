<?php

namespace App\Services\ReportCard\Dto;

class SubjectInput
{
    public function __construct(
        public int $subjectId,
        public string $name,
        public string $method,
        public bool $isRequired = true,
        public ?int $sortOrder = 0,
        public ?float $hk1Grade = null,
        public ?float $hk2Grade = null,
        public ?float $fullYearGradeManual = null,
        public ?string $hk1Level = null,
        public ?string $hk2Level = null,
        public ?string $fullYearLevelManual = null,
        public ?string $fullYearSourceManual = null,
        public ?float $grade = null,
        public ?string $achievementLevel = null,
        public ?string $remark = null,
    ) {
    }
}
