<?php

namespace App\Admin\Services\LessonCategory;

use Illuminate\Http\Request;

interface LessonCategoryServiceInterface
{
    public function store(Request $request);

    public function update(Request $request);

    public function delete($id);

    public function actionMultipleRecords(Request $request): bool;
}
