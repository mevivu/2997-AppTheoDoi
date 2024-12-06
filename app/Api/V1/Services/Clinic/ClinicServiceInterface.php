<?php

namespace App\Api\V1\Services\Clinic;


interface ClinicServiceInterface
{

    public function search(array $filters);
}
