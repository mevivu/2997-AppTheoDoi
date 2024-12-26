<?php

namespace App\Api\V1\Services\ChildEvaluation;


use Illuminate\Http\Request;

interface ChildEvaluationServiceInterface
{
    public function store(Request $request);

    public function index(Request $request);

    public function show($id);

    public function search(Request $request);

}
