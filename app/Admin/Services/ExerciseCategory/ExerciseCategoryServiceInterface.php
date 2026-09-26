<?php

namespace App\Admin\Services\ExerciseCategory;

use Illuminate\Http\Request;

interface ExerciseCategoryServiceInterface
{
    public function store(Request $request);

    public function update(Request $request);

    public function delete($id);

    public function actionMultipleRecords(Request $request): bool;
}
