<?php

namespace App\Admin\Services\MemoCard;

use Illuminate\Http\Request;

interface MemoCardServiceInterface
{
    public function store(Request $request);

    public function bulkStore(Request $request): int;

    public function update(Request $request);

    public function updatePosition(Request $request): bool;

    public function actionMultipleRecords(Request $request): bool;
}
