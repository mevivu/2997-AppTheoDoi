<?php

namespace App\Admin\Http\Requests\IqNorm;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\ActiveStatus;
use Illuminate\Validation\Rules\Enum;

class IqNormRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'age' => ['required', 'integer', 'between:1,20', 'unique:iq_norms,age'],
            'mean_score' => ['required', 'numeric', 'min:0'],
            'sd_score' => ['required', 'numeric', 'min:0.01'],
            'sample_size' => ['nullable', 'integer', 'min:0'],
            'min_raw_score' => ['nullable', 'numeric'],
            'max_raw_score' => ['nullable', 'numeric'],
            'version' => ['nullable', 'string', 'max:50'],
            'status' => ['required', new Enum(ActiveStatus::class)],
            'note' => ['nullable', 'string'],
        ];
    }

    protected function methodPut(): array
    {
        $id = $this->input('id');
        return [
            'id' => ['required', 'integer', 'exists:iq_norms,id'],
            'age' => ['required', 'integer', 'between:1,20', 'unique:iq_norms,age,' . $id],
            'mean_score' => ['required', 'numeric', 'min:0'],
            'sd_score' => ['required', 'numeric', 'min:0.01'],
            'sample_size' => ['nullable', 'integer', 'min:0'],
            'min_raw_score' => ['nullable', 'numeric'],
            'max_raw_score' => ['nullable', 'numeric'],
            'version' => ['nullable', 'string', 'max:50'],
            'status' => ['required', new Enum(ActiveStatus::class)],
            'note' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'age.required' => 'Vui lòng nhập độ tuổi.',
            'age.integer' => 'Độ tuổi phải là số nguyên.',
            'age.between' => 'Độ tuổi áp dụng phải từ 1 đến 20 tuổi.',
            'age.unique' => 'Độ tuổi này đã có cấu hình trong bảng chuẩn.',
            'mean_score.required' => 'Vui lòng nhập điểm thô trung bình (Mean μ).',
            'mean_score.numeric' => 'Điểm thô trung bình phải là số.',
            'sd_score.required' => 'Vui lòng nhập độ lệch chuẩn (StdDev σ).',
            'sd_score.numeric' => 'Độ lệch chuẩn phải là số.',
            'sd_score.min' => 'Độ lệch chuẩn phải lớn hơn 0.',
            'status.required' => 'Vui lòng chọn trạng thái kích hoạt.',
        ];
    }
}
