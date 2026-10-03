<?php

namespace App\Services\ReportCard\Dto;

class CalculationResult
{
    /**
     * @param SubjectResult[] $subjects
     * @param array $missing
     * @param string[] $warnings
     */
    public function __construct(
        public string $status,
        public ?string $rating = null,
        public array $subjects = [],
        public array $missing = [],
        public array $warnings = [],
        public array $adjustment = ['applied' => false, 'by_subject_id' => null, 'reason' => null],
        public array $rules = [],
        public string $regulation = '',
        public string $educationLevel = '',
        public string $semester = '',
        public int $version = 1,
        public ?string $scope = null,
    ) {
    }

    public static function incomplete(
        array $missing,
        array $subjects = [],
        array $warnings = [],
        string $regulation = '',
        string $educationLevel = '',
        string $semester = '',
        int $version = 1,
    ): self {
        return new self(
            status: 'incomplete',
            rating: null,
            subjects: $subjects,
            missing: $missing,
            warnings: $warnings,
            regulation: $regulation,
            educationLevel: $educationLevel,
            semester: $semester,
            version: $version,
        );
    }

    public static function invalidInput(
        array $warnings,
        array $subjects = [],
        string $regulation = '',
        string $educationLevel = '',
        string $semester = '',
        int $version = 1,
    ): self {
        return new self(
            status: 'invalid_input',
            rating: null,
            subjects: $subjects,
            missing: [],
            warnings: $warnings,
            regulation: $regulation,
            educationLevel: $educationLevel,
            semester: $semester,
            version: $version,
        );
    }

    public function isOk(): bool
    {
        return $this->status === 'ok';
    }

    public function snapshot(): array
    {
        return [
            'version' => $this->version,
            'regulation' => $this->regulation,
            'education_level' => $this->educationLevel,
            'semester' => $this->semester,
            'scope' => $this->scope,
            'status' => $this->status,
            'rating' => $this->rating,
            'subjects' => array_values(array_map(fn($s) => $s instanceof SubjectResult ? $s->toArray() : $s, $this->subjects)),
            'rules' => $this->rules,
            'adjustment' => $this->adjustment,
            'missing' => $this->missing,
            'warnings' => $this->warnings,
        ];
    }
}
