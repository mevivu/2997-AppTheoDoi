<?php

namespace App\Admin\Services\VaccinationSchedule;
use Illuminate\Http\Request;

interface VaccinationScheduleServiceInterface
{


    public function store(Request $request);

    public function update(Request $request);

    public function delete($id);

    public function actionMultipleRecords(Request $request);



}
