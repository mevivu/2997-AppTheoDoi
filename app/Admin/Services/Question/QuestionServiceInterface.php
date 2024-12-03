<?php

namespace App\Admin\Services\Question;

use Illuminate\Http\Request;

interface QuestionServiceInterface
{
    public function store(Request $request);

    public function update(Request $request);

    public function actionMultipleRecords(Request $request);
}
