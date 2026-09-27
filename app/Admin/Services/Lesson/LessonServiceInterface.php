<?php

namespace App\Admin\Services\Lesson;

use Illuminate\Http\Request;

interface LessonServiceInterface
{
    public function store(Request $request);

    public function update(Request $request);

    public function delete($id);

    public function actionMultipleRecords(Request $request): bool;
}
