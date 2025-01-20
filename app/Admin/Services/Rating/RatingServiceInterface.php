<?php

namespace App\Admin\Services\Rating;

use Illuminate\Http\Request;

interface RatingServiceInterface
{
    public function actionMultipleRecords(Request $request);
}
