<?php

namespace App\Admin\Services\Children;
use Illuminate\Http\Request;

interface ChildrenServiceInterface
{

    public function store(Request $request);

    public function update(Request $request);

    public function delete($id);

    public function actionMultipleRecode(Request $request);

}
