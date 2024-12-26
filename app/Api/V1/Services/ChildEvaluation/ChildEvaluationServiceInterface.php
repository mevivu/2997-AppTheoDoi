<?php

namespace App\Api\V1\Services\ChildEvaluation;


use Illuminate\Http\Request;

interface ChildEvaluationServiceInterface
{
    public function store(Request $request);

}
