<?php

namespace App\Api\V1\Http\Requests\ChildEvaluation;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\ActiveStatus;
use App\Enums\ChildEvaluation\AcademicRating;
use App\Enums\ChildEvaluation\ConductRating;
use App\Enums\ChildEvaluation\EvaluationStatus;
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


    protected function methodPut(): array
    {
        $rules = [
            'child_evaluation_id' => ['required', 'exists:App\Models\ChildEvaluation,id'],
            'status' => ['nullable', new Enum(ActiveStatus::class)],
            'conduct' => ['nullable', new Enum(ConductRating::class)],
            'academic_performance' => ['required', new Enum(AcademicRating::class)],
        ];

        if (request()->input('status') == ActiveStatus::Active->value) {
            $additionalRules = [
                'subjects' => ['required', 'array'],
                'subjects.*.id' => ['required', 'exists:subjects,id'],
                'subjects.*.grade' => ['required', 'numeric', 'between:0,10'],

                'qualities' => ['required', 'array'],
                'qualities.*.id' => ['required', 'exists:qualities,id'],
                'qualities.*.quality_status' => ['required', new Enum(EvaluationStatus::class)],

                'capabilities' => ['required', 'array'],
                'capabilities.*.id' => ['required', 'exists:capabilities,id'],
                'capabilities.*.capability_status' => ['required', new Enum(EvaluationStatus::class)],
            ];
            $rules = array_merge($rules, $additionalRules);
        } else {
            $rules['subjects'] = ['nullable', 'array'];
            $rules['qualities'] = ['nullable', 'array'];
            $rules['capabilities'] = ['nullable', 'array'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'child_id.required' => 'Trường child_id là bắt buộc.',
            'child_id.exists' => 'ID của trẻ không tồn tại trong hệ thống.',

            'limit.integer' => 'Giới hạn phải là một số nguyên.',
            'limit.min' => 'Giới hạn phải lớn hơn hoặc bằng 1.',

            'page.integer' => 'Số trang phải là một số nguyên.',
            'page.min' => 'Số trang phải lớn hơn hoặc bằng 1.',

            'child_evaluation_id.required' => 'Trường child_evaluation_id là bắt buộc.',
            'child_evaluation_id.exists' => 'Đánh giá trẻ em không tồn tại.',

            'status.required' => 'Trạng thái là trường bắt buộc.',
            'conduct.required' => 'Hạnh kiểm là trường bắt buộc.',
            'academic_performance.required' => 'Hiệu suất học tập là trường bắt buộc.',

            'subjects.required' => 'Môn học là trường bắt buộc.',
            'subjects.*.id.required' => 'ID môn học là trường bắt buộc.',
            'subjects.*.id.exists' => 'ID môn học không tồn tại.',
            'subjects.*.grade.required' => 'Điểm số là trường bắt buộc.',
            'subjects.*.grade.numeric' => 'Điểm số phải là một số.',
            'subjects.*.grade.between' => 'Điểm số phải từ 0 đến 10.',

            'qualities.required' => 'Các phẩm chất là trường bắt buộc.',
            'qualities.*.id.required' => 'ID phẩm chất là trường bắt buộc.',
            'qualities.*.id.exists' => 'ID phẩm chất không tồn tại.',
            'qualities.*.quality_status.required' => 'Trạng thái phẩm chất là trường bắt buộc.',

            'capabilities.required' => 'Năng lực là trường bắt buộc.',
            'capabilities.*.id.required' => 'ID năng lực là trường bắt buộc.',
            'capabilities.*.id.exists' => 'ID năng lực không tồn tại.',
            'capabilities.*.capability_status.required' => 'Trạng thái năng lực là trường bắt buộc.',
        ];
    }


}
