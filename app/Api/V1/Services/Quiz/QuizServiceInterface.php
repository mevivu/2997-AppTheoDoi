<?php

namespace App\Api\V1\Services\Quiz;


use Illuminate\Http\Request;

interface QuizServiceInterface
{
    public function store(Request $request);

    public function update(Request $request);

    public function delete($id);

    public function index(Request $request);

}
