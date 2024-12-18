<?php

namespace App\Api\V1\Services\Diaries;

use Illuminate\Http\Request;

interface DiariesServiceInterface
{

    public function store(Request $request);

    public function update(Request $request);

}
