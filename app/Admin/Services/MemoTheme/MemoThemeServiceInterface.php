<?php

namespace App\Admin\Services\MemoTheme;

use Illuminate\Http\Request;

interface MemoThemeServiceInterface
{
    public function store(Request $request);

    public function update(Request $request);

    public function actionMultipleRecords(Request $request): bool;
}
