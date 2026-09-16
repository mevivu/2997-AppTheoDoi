<?php

namespace App\Admin\Services\MemoAgeConfig;

use Illuminate\Http\Request;

interface MemoAgeConfigServiceInterface
{
    public function store(Request $request);

    public function update(Request $request);

    public function actionMultipleRecords(Request $request): bool;
}
