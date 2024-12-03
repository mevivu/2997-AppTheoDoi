<?php

namespace App\Admin\Services\QuestionGroup;

use Illuminate\Http\Request;

interface QuestionGroupServiceInterface
{
    public function store(Request $request);

    public function update(Request $request);

    public function actionMultipleRecords(Request $request);
}