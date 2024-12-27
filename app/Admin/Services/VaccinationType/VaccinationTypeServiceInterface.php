<?php

namespace App\Admin\Services\VaccinationType;

use Illuminate\Http\Request;

interface VaccinationTypeServiceInterface
{


    public function store(Request $request);

    public function update(Request $request);

    public function delete($id);

    public function actionMultipleRecords(Request $request);

}
