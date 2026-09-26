<?php

namespace App\Admin\Services\Video;

use Illuminate\Http\Request;

interface VideoServiceInterface
{
    public function store(Request $request);

    public function update(Request $request);

    public function delete($id);

    public function actionMultipleRecords(Request $request): bool;
}
