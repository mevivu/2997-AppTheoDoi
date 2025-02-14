<?php

namespace App\Admin\Services\Product;

use Illuminate\Http\Request;

interface ProductServiceInterface
{
    public function store(Request $request);

    public function update(Request $request);

    public function delete($id);
    public function actionMultipleRecords(Request $request): bool;

}
