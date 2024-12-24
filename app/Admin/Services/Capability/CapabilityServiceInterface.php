<?php

namespace App\Admin\Services\Capability;

use Illuminate\Http\Request;

interface CapabilityServiceInterface
{
    public function store(Request $request);

    public function update(Request $request);

    public function actionMultipleRecords(Request $request);
}
