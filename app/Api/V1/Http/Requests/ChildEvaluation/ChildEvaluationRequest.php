<?php

namespace App\Api\V1\Http\Requests\ChildEvaluation;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\ActiveStatus;
use App\Enums\ChildEvaluation\AcademicRating;
use App\Enums\ChildEvaluation\ConductRating;
use App\Enums\ChildEvaluation\EvaluationStatus;
use App\Enums\Semester\SemesterStatus;
use Illuminate\Validation\Rules\Enum;

class ChildEvaluationRequest extends BaseRequest
{

    protected function methodGet(): array
    {
        return [
            'child_id' => ['required', 'exists:App\Models\Child,id'],
            'limit' => 'nullable|integer|min:1',
            'page' => 'nullable|integer|min:1',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost(): array
    {
        return [

            'class_grade_id' => ['required', 'exists:App\Models\ClassGrade,id'],
            'semester' => ['required', new Enum(SemesterStatus::class)],
            'status' => ['required', new Enum(ActiveStatus::class)],
            'conduct' => ['required', new Enum(ConductRating::class)],
            'academic_performance' => ['required', new Enum(AcademicRating::class)],

            'subjects' => ['required', 'array'],
            'subjects.*.id' => ['required', 'exists:subjects,id'],
            'subjects.*.grade' => ['required', 'numeric', 'between:0,10'],

            'qualities' => ['required', 'array'],
            'qualities.*.id' => ['required', 'exists:qualities,id'],
            'qualities.*.quality_status' => ['required', new Enum(EvaluationStatus::class)],

            'capabilities' => ['required', 'array'],
            'capabilities.*.id' => ['required', 'exists:qualities,id'],
            'capabilities.*.capability_status' => ['required', new Enum(EvaluationStatus::class)],


        ];
    }

    protected function methodPut(): array
    {
        return [

            'child_evaluation_id' => ['required', 'exists:App\Models\ChildEvaluation,id'],
//            'class_grade_id' => ['required', 'exists:App\Models\ClassGrade,id'],
//            'semester' => ['required', new Enum(SemesterStatus::class)],
            'status' => ['required', new Enum(ActiveStatus::class)],
            'conduct' => ['required', new Enum(ConductRating::class)],
            'academic_performance' => ['required', new Enum(AcademicRating::class)],

            'subjects' => ['required', 'array'],
            'subjects.*.id' => ['required', 'exists:subjects,id'],
            'subjects.*.grade' => ['required', 'numeric', 'between:0,10'],

            'qualities' => ['required', 'array'],
            'qualities.*.id' => ['required', 'exists:qualities,id'],
            'qualities.*.quality_status' => ['required', new Enum(EvaluationStatus::class)],

            'capabilities' => ['required', 'array'],
            'capabilities.*.id' => ['required', 'exists:qualities,id'],
            'capabilities.*.capability_status' => ['required', new Enum(EvaluationStatus::class)],


        ];
    }


}
