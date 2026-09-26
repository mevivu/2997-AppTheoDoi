<?php

namespace App\Admin\Services\ExerciseMedia;

use Illuminate\Http\Request;

interface ExerciseMediaServiceInterface
{
    public function store(Request $request);

    public function delete($id);
}
