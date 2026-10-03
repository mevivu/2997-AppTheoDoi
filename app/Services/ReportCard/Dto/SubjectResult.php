<?php

namespace App\Services\ReportCard\Dto;

class SubjectResult
{
    public function __construct(
        public int $subjectId,
        public string $name,
        public string $method,
        public bool $isRequired = true,
        public float|string|null $value = null,
        public ?string $source = null,
        public ?string $formula = null,
        public float|string|null $hk1 = null,
        public float|string|null $hk2 = null,
        public ?bool $passed = null,
        public array $notes = [],
    ) {
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->subjectId,
            'name' => $this->name,
            'method' => $this->method,
            'is_required' => $this->isRequired,
            'value' => $this->value,
            'source' => $this->source,
            'formula' => $this->formula,
            'hk1' => $this->hk1,
            'hk2' => $this->hk2,
            'passed' => $this->passed,
            'notes' => empty($this->notes) ? null : $this->notes,
        ], fn($v) => $v !== null);
    }
}
