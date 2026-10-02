<?php

namespace App\Admin\Services\Expert;

use Illuminate\Http\Request;

interface ExpertServiceInterface
{
    public function store(Request $request);
    public function update(Request $request);
    public function delete($id);
    public function actionMultipleRecode(Request $request): bool;
}
