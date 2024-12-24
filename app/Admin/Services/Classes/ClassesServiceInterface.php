<?php

namespace App\Admin\Services\Classes;

use Illuminate\Http\Request;

interface ClassesServiceInterface
{


    public function store(Request $request);

    public function update(Request $request);

    public function actionMultipleRecords(Request $request);

}
