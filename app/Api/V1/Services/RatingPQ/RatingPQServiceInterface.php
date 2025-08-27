<?php

namespace App\Api\V1\Services\RatingPQ;


use Illuminate\Http\Request;

interface RatingPQServiceInterface
{
    public function store(Request $request);

    public function update(Request $request);

    public function delete($id);

    public function index(Request $request);

    public function getOverallStats(Request $request, $optionChildId = null);

    public function getMonthlyEnduranceData(Request $request);

    public function getScorePQ($request, $childId): ?float;


}
