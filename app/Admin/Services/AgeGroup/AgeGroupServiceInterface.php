<?php

namespace App\Admin\Services\AgeGroup;

use Illuminate\Http\Request;

interface AgeGroupServiceInterface
{
    public function store(Request $request);

    public function update(Request $request);

    public function delete($id);

    public function actionMultipleRecords(Request $request): bool;
}
