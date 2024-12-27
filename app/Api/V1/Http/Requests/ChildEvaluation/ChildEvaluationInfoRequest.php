<?php

namespace App\Api\V1\Http\Requests\ChildEvaluation;

use App\Api\V1\Http\Requests\BaseRequest;

class ChildEvaluationInfoRequest extends BaseRequest
{

    protected function methodGet(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\ClassGrade,id'],
        ];
    }




}
