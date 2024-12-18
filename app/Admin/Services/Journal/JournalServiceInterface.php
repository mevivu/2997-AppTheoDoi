<?php

namespace App\Admin\Services\Journal;

use Illuminate\Http\Request;

interface JournalServiceInterface
{

    public function store(Request $request);

    public function update(Request $request);

    public function delete($id);


}
