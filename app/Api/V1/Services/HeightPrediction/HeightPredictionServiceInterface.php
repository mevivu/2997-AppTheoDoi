<?php

namespace App\Api\V1\Services\HeightPrediction;


use Illuminate\Http\Request;

interface HeightPredictionServiceInterface
{

    public function index(Request $request);

    public function calculateMatureHeight($child, $currentHeight, $latestDate): float;

    public function chart(Request $request): array;

}
