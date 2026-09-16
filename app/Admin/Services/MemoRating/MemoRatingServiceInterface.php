<?php

namespace App\Admin\Services\MemoRating;

use Illuminate\Http\Request;

interface MemoRatingServiceInterface
{
    public function delete($id);

    public function actionMultipleRecords(Request $request): bool;
}
