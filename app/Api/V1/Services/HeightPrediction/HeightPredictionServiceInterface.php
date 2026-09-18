<?php

namespace App\Api\V1\Services\HeightPrediction;


use Illuminate\Http\Request;

interface HeightPredictionServiceInterface
{

    public function index(Request $request);

    public function calculateMatureHeight($child, $currentHeight, $latestDate, float $pubertyMonths = 0.0): float;

    public function calculateMatureHeightAt19($child, $currentHeight, $latestDate, float $pubertyMonths = 0.0): float;

    public function chart(Request $request): array;

    public function indexV2(Request $request): array;

    public function chartV2(Request $request): array;

    public function debugHeightRegimen(int $childId, float $pubertyMonths = 0.0, ?float $targetHeight = null): array;
}
