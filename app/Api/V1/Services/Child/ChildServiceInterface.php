<?php

namespace App\Api\V1\Services\Child;


use Illuminate\Http\Request;

interface ChildServiceInterface
{
    public  function store(Request $request);
}
