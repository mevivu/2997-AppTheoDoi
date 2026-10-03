<?php

namespace App\Admin\Services\FetalGrowthStandard;

use Illuminate\Http\Request;

interface FetalGrowthStandardServiceInterface
{
    public function store(Request $request);

    public function update(Request $request);

    public function delete($id);

    public function actionMultipleRecords(Request $request): bool;
}
