<?php

namespace App\Admin\Services\ExpertPost;

use Illuminate\Http\Request;

interface ExpertPostServiceInterface
{
    public function store(Request $request);
    public function update(Request $request);
    public function delete($id);
    public function actionMultipleRecode(Request $request): bool;
}
