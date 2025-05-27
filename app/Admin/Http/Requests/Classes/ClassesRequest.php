<?php

namespace App\Admin\Http\Requests\Classes;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\ActiveStatus;
use App\Enums\Class\LevelGroup;
use Illuminate\Validation\Rules\Enum;

class ClassesRequest extends BaseRequest
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
            'subject_id' => ['required', 'array'],
            'subject_id.*' => 'required|exists:subjects,id',
            'status' => ['required', new Enum(ActiveStatus::class)],
            'level_group' => ['required', new Enum(LevelGroup::class)],
        ];
    }
    protected function methodPut(): array
    {
        return [
            'id'=>['required', 'integer', 'exists:App\Models\SchoolClass,id'],
            'name' => ['required', 'string'],
            'status' => ['required', new Enum(ActiveStatus::class)],
            'subject_id' => ['required', 'array'],
            'Subject_id.*' => 'required|exists:subjects,id',
            'level_group' => ['required', new Enum(LevelGroup::class)],
        ];
    }
}
