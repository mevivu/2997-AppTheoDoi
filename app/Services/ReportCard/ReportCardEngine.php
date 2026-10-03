<?php

namespace App\Services\ReportCard;

use App\Services\ReportCard\Dto\CalculationResult;
use App\Services\ReportCard\Dto\ReportCardInput;
use Throwable;

class ReportCardEngine
{
    public const VERSION = 1;

    public function __construct(
        protected ?CalculatorResolver $resolver = null
    ) {
        $this->resolver = $resolver ?? new CalculatorResolver();
    }

    public function calculate(ReportCardInput $input): CalculationResult
    {
        try {
            $calculator = $this->resolver->resolve($input->educationLevel, $input->classId);
            return $calculator->calculate($input);
        } catch (Throwable $e) {
            return CalculationResult::invalidInput(
                warnings: ['Lỗi tính toán hệ thống: ' . $e->getMessage()],
                regulation: '',
                educationLevel: $input->educationLevel,
                semester: $input->semester,
                version: self::VERSION,
            );
        }
    }
}
