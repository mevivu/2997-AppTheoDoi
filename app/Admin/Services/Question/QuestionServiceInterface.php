<?php

namespace App\Admin\Services\Question;

use Illuminate\Http\Request;

interface QuestionServiceInterface
{
    public function storeIq(Request $request);

    public function storeEqAq(Request $request);

    public function updateIq(Request $request);

    public function updateEqAq(Request $request);

    public function actionMultipleRecords(Request $request);
}