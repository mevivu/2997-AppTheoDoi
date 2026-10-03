<?php

namespace App\Services\ReportCard\Contracts;

use App\Services\ReportCard\Dto\CalculationResult;
use App\Services\ReportCard\Dto\ReportCardInput;

interface ReportCardCalculator
{
    public function calculate(ReportCardInput $input): CalculationResult;
}
