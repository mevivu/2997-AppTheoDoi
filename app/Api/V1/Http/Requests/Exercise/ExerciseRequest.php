<?php

namespace App\Api\V1\Http\Requests\Exercise;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\Exercise\ExerciseType;

use Google\Protobuf\EnumValue;
use Illuminate\Validation\Rules\Enum;

class ExerciseRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodGet()
    {
        return [
            'page' => ['nullable', 'integer', 'min:1'],
            'limit' => ['nullable', 'integer', 'min:1'],
            'exercise_type' => ['nullable', new Enum(ExerciseType::class)],

        ];
    }
}
