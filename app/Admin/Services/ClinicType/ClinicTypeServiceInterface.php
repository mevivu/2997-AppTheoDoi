<?php

namespace App\Admin\Services\ClinicType;
use Illuminate\Http\Request;

interface ClinicTypeServiceInterface
{


    public function store(Request $request);

    public function update(Request $request);

    public function delete($id);

    public function actionMultipleRecords(Request $request);



}
