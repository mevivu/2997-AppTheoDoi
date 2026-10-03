<?php

namespace App\Services\ReportCard\Dto;

class ReportCardInput
{
    /**
     * @param SubjectInput[] $subjects
     * @param array $qualities
     * @param array $capabilities
     */
    public function __construct(
        public string $educationLevel,
        public int $classId,
        public string $semester,
        public array $subjects = [],
        public array $qualities = [],
        public array $capabilities = [],
        public ?string $currentAcademicPerformance = null,
        public bool $isPerformanceOverridden = false,
    ) {
    }
}
