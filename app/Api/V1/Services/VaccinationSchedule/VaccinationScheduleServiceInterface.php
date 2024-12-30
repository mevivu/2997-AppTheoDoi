<?php

namespace App\Api\V1\Services\VaccinationSchedule;

use Illuminate\Http\Request;

interface VaccinationScheduleServiceInterface
{
    public function index(Request $request);
    public function store(Request $request);
    public function update(Request $request);
    public function delete($id);
}
