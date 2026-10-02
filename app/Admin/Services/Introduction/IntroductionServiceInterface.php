<?php

namespace App\Admin\Services\Introduction;

use Illuminate\Http\Request;

interface IntroductionServiceInterface
{
    public function store(Request $request);
    public function update(Request $request);
    public function delete($id);
    public function actionMultipleRecode(Request $request): bool;
}
