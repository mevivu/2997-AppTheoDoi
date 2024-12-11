<?php

namespace App\Admin\Services\WeightHeightWho;

use Illuminate\Http\Request;

interface WeightHeightWhoServiceInterface
{
    /**
     * Tạo mới
     *
     * @var Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function store(Request $request);
    public function update(Request $request);
    public function actionMultipleRecords(Request $request);

}
