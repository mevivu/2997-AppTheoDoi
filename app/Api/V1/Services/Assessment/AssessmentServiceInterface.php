<?php

namespace App\Api\V1\Services\Assessment;


use Illuminate\Http\Request;

interface AssessmentServiceInterface
{

    public function index(Request $request);

}
