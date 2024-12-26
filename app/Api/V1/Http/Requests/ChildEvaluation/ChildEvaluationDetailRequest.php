<?php

namespace App\Api\V1\Http\Requests\ChildEvaluation;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\ActiveStatus;
use App\Enums\ChildEvaluation\AcademicRating;
use App\Enums\ChildEvaluation\ConductRating;
use App\Enums\ChildEvaluation\EvaluationStatus;
use App\Enums\Semester\SemesterStatus;
use Illuminate\Validation\Rules\Enum;

class ChildEvaluationDetailRequest extends BaseRequest
{

    protected function methodGet(): array
    {
        return [
            'child_id' => ['required', 'exists:App\Models\Child,id'],
            'limit' => 'nullable|integer|min:1',
            'page' => 'nullable|integer|min:1',
        ];
    }




}
