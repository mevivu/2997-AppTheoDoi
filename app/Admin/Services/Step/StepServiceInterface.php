<?php

namespace App\Admin\Services\Step;
use Illuminate\Http\Request;

interface StepServiceInterface
{


    public function store(Request $request);

    public function update(Request $request);

    public function delete($id);

    public function actionMultipleRecords(Request $request);




}
