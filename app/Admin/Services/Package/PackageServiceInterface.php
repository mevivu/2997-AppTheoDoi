<?php

namespace App\Admin\Services\Package;
use Illuminate\Http\Request;

interface PackageServiceInterface
{


    public function store(Request $request);

    public function update(Request $request);

    public function delete($id);

    public function actionMultipleRecords(Request $request);



}
