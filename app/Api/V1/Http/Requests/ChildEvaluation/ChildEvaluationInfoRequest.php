<?php

namespace App\Api\V1\Http\Requests\ChildEvaluation;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\Semester\SemesterStatus;
use Illuminate\Validation\Rules\Enum;

class ChildEvaluationInfoRequest extends BaseRequest
{

    protected function methodGet(): array
    {
        return [
            'class_id' => ['required', 'exists:App\Models\SchoolClass,id'],
            'child_id' => ['required', 'exists:App\Models\Child,id'],
            'semester' => ['required', new Enum(SemesterStatus::class)],
        ];
    }




}
