<?php

namespace App\Admin\Http\Requests\Subject;

use App\Admin\Http\Requests\BaseRequest;

class SubjectRequest extends BaseRequest
{
    public function methodPost()
    {
        return [
            'name' => 'required',
            'class_id' => 'required|exists:classes,id',
            'status' => 'required',
        ];
    }

    public function methodPut()
    {
        return [
            'id' => 'required|exists:subjects,id',
            'name' => 'required',
            'class_id' => 'required|exists:classes,id',
            'status' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Vui lòng nhập tên',
            'status.required' => 'Vui lòng chọn trạng thái',
            'class_id.required' => 'Vui lòng chọn lớp',
            'class_id.exists' => 'Lớp không tồn tại',
            'id.required' => 'Id không được để trống',
            'id.exists' => 'Id không tồn tại',
        ];
    }
}