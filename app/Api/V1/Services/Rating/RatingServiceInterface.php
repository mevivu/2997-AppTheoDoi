<?php

namespace App\Api\V1\Services\Rating;


use Illuminate\Http\Request;

interface RatingServiceInterface
{
    public function store(Request $request);

    public function update(Request $request);

    public function delete($id);

    public function index(Request $request);

}
