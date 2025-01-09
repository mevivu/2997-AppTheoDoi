<?php

namespace App\Admin\Services\Guide;
use Illuminate\Http\Request;

interface GuideServiceInterface
{


    public function store(Request $request);

    public function update(Request $request);

    public function delete($id);

    public function actionMultipleRecords(Request $request);



}
