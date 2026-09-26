<?php

namespace App\Admin\Services\VideoCategory;

use Illuminate\Http\Request;

interface VideoCategoryServiceInterface
{
    public function store(Request $request);

    public function update(Request $request);

    public function delete($id);

    public function actionMultipleRecords(Request $request): bool;
}
