<?php

namespace App\Api\V1\Services\Clinic;


use App\Models\User;
use Illuminate\Http\Request;

interface ClinicServiceInterface
{

public function searchClinics(array $filters);
}
