<?php

namespace App\Admin\Services\RatingPQ;

use Illuminate\Http\Request;

interface RatingPQServiceInterface
{
    public function actionMultipleRecords(Request $request);
}
