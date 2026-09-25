<?php

namespace App\Admin\Http\Requests\Memo;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\ActiveStatus;
use App\Enums\Memo\MemoConfigType;
use Illuminate\Validation\Rules\Enum;

class MemoAgeConfigRequest extends BaseRequest
{
    protected function prepareForValidation(): void
    {
        if (!$this->has('type') || empty($this->input('type'))) {
            $this->merge(['type' => MemoConfigType::IqTest->value]);
        }
    }

    protected function methodPost(): array
    {
        return [
            'name' => ['required', 'string', 'max:191'],
            'type' => ['nullable', new Enum(MemoConfigType::class)],
            'min_age' => ['required', 'integer', 'min:1'],
            'max_age' => ['required', 'integer', 'gte:min_age'],
            'rows' => ['required', 'integer', 'min:2', 'max:10'],
            'columns' => ['required', 'integer', 'min:2', 'max:10'],
            'total_duration' => ['required', 'integer', 'min:30', 'max:600'],
            'total_rounds' => ['required', 'integer', 'min:1', 'max:10'],
            'peek_time' => ['required', 'integer', 'min:0', 'max:30'],
            'max_moves' => ['nullable', 'integer', 'min:0', 'max:300'],
            'status' => ['required', new Enum(ActiveStatus::class)],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:memo_age_configs,id'],
            'name' => ['required', 'string', 'max:191'],
            'type' => ['nullable', new Enum(MemoConfigType::class)],
            'min_age' => ['required', 'integer', 'min:1'],
            'max_age' => ['required', 'integer', 'gte:min_age'],
            'rows' => ['required', 'integer', 'min:2', 'max:10'],
            'columns' => ['required', 'integer', 'min:2', 'max:10'],
            'total_duration' => ['required', 'integer', 'min:30', 'max:600'],
            'total_rounds' => ['required', 'integer', 'min:1', 'max:10'],
            'peek_time' => ['required', 'integer', 'min:0', 'max:30'],
            'max_moves' => ['nullable', 'integer', 'min:0', 'max:300'],
            'status' => ['required', new Enum(ActiveStatus::class)],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $rows = (int) $this->input('rows');
            $cols = (int) $this->input('columns');
            if (($rows * $cols) % 2 !== 0) {
                $validator->errors()->add('columns', 'Tổng số thẻ (Số hàng x Số cột = ' . ($rows * $cols) . ') phải là một số chẵn để có thể tạo thành các cặp trùng nhau.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên mức độ cấu hình',
            'min_age.required' => 'Vui lòng nhập tuổi bắt đầu',
            'max_age.required' => 'Vui lòng nhập tuổi kết thúc',
            'max_age.gte' => 'Tuổi kết thúc phải lớn hơn hoặc bằng tuổi bắt đầu',
            'rows.required' => 'Vui lòng nhập số hàng',
            'columns.required' => 'Vui lòng nhập số cột',
            'total_duration.required' => 'Vui lòng nhập tổng thời gian bài test (giây)',
            'total_rounds.required' => 'Vui lòng nhập số lượt game trong bài test',
            'status.required' => 'Vui lòng chọn trạng thái',
        ];
    }
}
