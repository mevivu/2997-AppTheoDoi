<?php

namespace App\Api\V1\Services\Quiz;


use Illuminate\Http\Request;

interface QuizServiceInterface
{
    public function getListIQ(Request $request);

    public function getListAQAndEQ(Request $request);

    public function getRandomEQ(Request $request);
}
