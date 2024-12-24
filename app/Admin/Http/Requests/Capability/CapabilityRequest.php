<?php

namespace App\Admin\Http\Requests\Capability;

use App\Admin\Http\Requests\BaseRequest;

class CapabilityRequest extends BaseRequest
{
    public function methodPost()
    {
        return [
            'name' => 'required',
            'status' => 'required',
        ];
    }

    public function methodPut()
    {
        return [
            'id' => 'required|exists:capabilities,id',
            'name' => 'required',
            'status' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Vui lòng nhập tên',
            'status.required' => 'Vui lòng chọn trạng thái',
            'id.required' => 'Id không được để trống',
            'id.exists' => 'Id không tồn tại',
        ];
    }
}
