<?php

namespace App\Api\V1\Services\Child;


use Illuminate\Http\Request;

interface ChildServiceInterface
{
    public  function store(Request $request);

    public  function update(Request $request);

    public function index(Request $request);
}
