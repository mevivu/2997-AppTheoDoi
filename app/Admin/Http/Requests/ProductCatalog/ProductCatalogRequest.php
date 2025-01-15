<?php

namespace App\Admin\Http\Requests\ProductCatalog;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\ActiveStatus;
use Illuminate\Validation\Rules\Enum;


class ProductCatalogRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost(): array
    {
        return [
            'name' => ['required', 'string'],

        ];
    }

    protected function methodPut(): array
    {

        return [
            'id' => ['required', 'exists:App\Models\ProductCatalog,id'],
            'name' => ['required', 'string'],
            'status' => ['required', new Enum(ActiveStatus::class)],
        ];
    }
}
