<?php

namespace App\Admin\Services\Exercise;

use Illuminate\Http\Request;

interface ExerciseServiceInterface
{
    public function store(Request $request);

    public function update(Request $request);

    public function actionMultipleRecords(Request $request);
}