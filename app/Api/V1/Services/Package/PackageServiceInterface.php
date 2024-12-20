<?php

namespace App\Api\V1\Services\Package;



use Illuminate\Http\Request;

interface PackageServiceInterface
{

    public function index();
    public function purchasePackage(Request $request);

}
