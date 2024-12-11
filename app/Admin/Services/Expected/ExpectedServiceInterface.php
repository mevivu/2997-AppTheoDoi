<?php

namespace App\Admin\Services\Expected;

use Illuminate\Http\Request;

interface ExpectedServiceInterface
{
    public function store(Request $request);

    public function update(Request $request);

    public function actionMultipleRecords(Request $request);
}
