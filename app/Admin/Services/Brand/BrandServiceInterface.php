<?php

namespace App\Admin\Services\Brand;

use Illuminate\Http\Request;

interface BrandServiceInterface
{
    /**
     * Store a new brand.
     *
     * @throws Exception
     */
    public function store(Request $request): object|false;

    /**
     * Update an existing brand.
     *
     * @throws Exception
     */
    public function update(Request $request): object|bool;

    /**
     * Delete a brand.
     *
     * @throws Exception
     */
    public function delete($id): object;

    /**
     * Perform actions on multiple brands (e.g. change status).
     *
     * @throws Exception
     */
    public function actionMultipleRecords(Request $request): bool;
}
