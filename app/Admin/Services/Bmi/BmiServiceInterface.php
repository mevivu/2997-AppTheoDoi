<?php

namespace App\Admin\Services\Bmi;

use Illuminate\Http\Request;

interface BmiServiceInterface
{
    public function store(Request $request);

    public function update(Request $request);

    public function actionMultipleRecords(Request $request);
}