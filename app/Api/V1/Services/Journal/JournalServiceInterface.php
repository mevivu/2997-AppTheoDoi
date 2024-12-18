<?php

namespace App\Api\V1\Services\Journal;


use Illuminate\Http\Request;

interface JournalServiceInterface
{
    public function store(Request $request);

    public function update(Request $request);

    public function delete($id);

    public function index(Request $request);

}
