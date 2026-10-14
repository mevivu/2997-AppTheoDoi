<?php

namespace App\Admin\Services\IqNorm;

use Illuminate\Http\Request;

interface IqNormServiceInterface
{
    public function store(Request $request);

    public function update(Request $request);

    public function actionMultipleRecords(Request $request): bool;

    public function recalculateFromRatings(): array;
}
