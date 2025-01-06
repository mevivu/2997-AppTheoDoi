<?php

namespace App\Admin\Services\ProductCatalog;
use Illuminate\Http\Request;

interface ProductCatalogServiceInterface
{


    public function store(Request $request);

    public function update(Request $request);

    public function delete($id);

    public function actionMultipleRecords(Request $request);



}
