<?php

namespace App\Admin\Http\Requests\Permission;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\Permission\PermissionType;
use Illuminate\Validation\Rules\Enum;

class PermissionRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost()
    {
        return [
            'title' => ['required', 'string'],
            'name' => ['required', 'string'],
            'guard_name' => ['required', 'string'],
            'type' => ['required', new Enum(PermissionType::class)],
            'module_id' => ['nullable', 'int'],
        ];
    }

    protected function methodPut()
    {
        return [
            'id' => ['required', 'exists:App\Models\Permission,id'],
            'title' => ['required', 'string'],
            'name' => ['required', 'string'],
            'guard_name' => ['required', 'string'],
            'type' => ['required', new Enum(PermissionType::class)],
            'module_id' => ['nullable', 'int'],
        ];
    }
}