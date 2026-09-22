<?php

namespace App\Admin\Http\Requests\Memo;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\ActiveStatus;
use Illuminate\Validation\Rules\Enum;

class MemoThemeRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'name' => ['required', 'string', 'max:191'],
            'code' => ['required', 'string', 'max:50', 'unique:memo_themes,code'],
            'age' => ['required', 'integer', 'min:1', 'max:20'],
            'description' => ['nullable', 'string'],
            'position' => ['nullable', 'integer'],
            'status' => ['required', new Enum(ActiveStatus::class)],
            'icon' => ['nullable'],
            'card_back' => ['nullable'],
            'card_back_type' => ['nullable', 'string', 'in:theme,logo'],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:memo_themes,id'],
            'name' => ['required', 'string', 'max:191'],
            'code' => ['required', 'string', 'max:50', 'unique:memo_themes,code,' . $this->input('id')],
            'age' => ['required', 'integer', 'min:1', 'max:20'],
            'description' => ['nullable', 'string'],
            'position' => ['nullable', 'integer'],
            'status' => ['required', new Enum(ActiveStatus::class)],
            'icon' => ['nullable'],
            'card_back' => ['nullable'],
            'card_back_type' => ['nullable', 'string', 'in:theme,logo'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên chủ đề',
            'code.required' => 'Vui lòng nhập mã chủ đề (ví dụ: vehicles, flowers, numbers, flags)',
            'code.unique' => 'Mã chủ đề này đã tồn tại trên hệ thống',
            'age.required' => 'Vui lòng nhập độ tuổi áp dụng cho chủ đề',
            'age.min' => 'Độ tuổi áp dụng tối thiểu là 1 tuổi',
            'status.required' => 'Vui lòng chọn trạng thái',
        ];
    }
}
