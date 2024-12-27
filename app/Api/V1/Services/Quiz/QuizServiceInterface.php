<?php

namespace App\Api\V1\Services\Quiz;


use Illuminate\Http\Request;

interface QuizServiceInterface
{
    public function index(Request $request);
}
