<?php

namespace App\Api\V1\Services\RatingPQ;


use Illuminate\Http\Request;

interface RatingPQServiceInterface
{
    public function store(Request $request);

    public function delete($id);

    public function index(Request $request);



}
