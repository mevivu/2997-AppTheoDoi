<?php

namespace App\Admin\Services\Subject;

use Illuminate\Http\Request;

interface SubjectServiceInterface
{
    public function store(Request $request);

    public function update(Request $request);

    public function actionMultipleRecords(Request $request);
}
