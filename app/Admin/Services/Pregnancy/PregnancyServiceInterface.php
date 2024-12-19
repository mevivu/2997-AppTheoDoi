<?php

namespace App\Admin\Services\Pregnancy;

use Illuminate\Http\Request;

interface PregnancyServiceInterface
{
    public function store(Request $request);

    public function update(Request $request);

    public function actionMultipleRecords(Request $request);
}
