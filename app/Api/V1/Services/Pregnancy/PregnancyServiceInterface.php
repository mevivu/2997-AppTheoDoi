<?php

namespace App\Api\V1\Services\Pregnancy;


use Illuminate\Http\Request;

interface PregnancyServiceInterface
{
    public function store(Request $request);

    public function update(Request $request);

    public function delete($id);

    public function index(Request $request);

}
