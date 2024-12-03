<?php

namespace App\Admin\Services\Clinic;
use Illuminate\Http\Request;

interface ClinicServiceInterface
{


    public function store(Request $request);

    public function update(Request $request);

    public function delete($id);

    public function actionMultipleRecords(Request $request);



}
