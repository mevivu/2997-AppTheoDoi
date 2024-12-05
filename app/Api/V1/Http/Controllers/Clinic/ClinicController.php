<?php

namespace App\Api\V1\Http\Controllers\Clinic;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Services\Clinic\ClinicServiceInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;

/**
 * @group Phòng khám
 */

class ClinicController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    public function __construct(
        ClinicServiceInterface $service

    ) {
        $this->service = $service;
    }

}
