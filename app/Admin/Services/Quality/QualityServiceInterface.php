<?php

namespace App\Admin\Services\Quality;

use Illuminate\Http\Request;

interface QualityServiceInterface
{
    public function store(Request $request);

    public function update(Request $request);

    public function actionMultipleRecords(Request $request);
}