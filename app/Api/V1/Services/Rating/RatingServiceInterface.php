<?php

namespace App\Api\V1\Services\Rating;


use Illuminate\Http\Request;

interface RatingServiceInterface
{
    public function storeIQ(Request $request);

    public function storeEQAndAQ(Request $request);

    public function delete($id);

    public function index(Request $request);

}
