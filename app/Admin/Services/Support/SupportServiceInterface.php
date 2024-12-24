<?php

namespace App\Admin\Services\Support;

use Illuminate\Http\Request;

interface SupportServiceInterface
{
    public function store(Request $request);

    public function update(Request $request);

    public function actionMultipleRecords(Request $request);
}