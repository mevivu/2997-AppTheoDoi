<?php

namespace App\Admin\Http\Requests\Notification;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\ApprovalStatus;
use App\Enums\Notification\NotificationStatus;
use App\Enums\Notification\NotificationType;
use Illuminate\Validation\Rules\Enum;

class NotificationRequest extends BaseRequest
{
    protected function methodGet(): array
    {
        return [
            'admin_id' => 'required|exists:admins,id',
        ];
    }

    protected function methodPost(): array
    {
        return [
            'types' => ['required', new Enum(NotificationType::class)],
            'option' => ['nullable'],
            'user_id' => ['nullable'],
            'admin_id' => ['nullable'],
            'title' => ['required', 'string'],
            'message' => ['required'],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\Notification,id'],
            'title' => ['required', 'string'],
            'message' => ['required'],
            'status' => ['required', new Enum(NotificationStatus::class)],
            'approval_status' => ['required', new Enum(ApprovalStatus::class)],
        ];
    }

    protected function methodPatch(): array
    {
        return [
            'admin_id' => 'required|exists:admins,id',
        ];
    }

    public function messages()
    {
        return [
            'types.required' => 'Vui lòng chọn đối tượng thông báo',
            'types.enum' => 'Đối tượng thông báo không hợp lệ',
            'option.enum' => 'Lựa chọn thông báo không hợp lệ',
            'user_id.exists' => 'Id người dùng không hợp lệ',
            'admin_id.exists' => 'Id admin không hợp lệ',
            'title.required' => 'Tiêu đề không được để trống',
            'message.required' => 'Nội dung không được để trống',
            'status.required' => 'Trạng thái không được để trống',
            'status.enum' => 'Trạng thái không hợp lệ',
            'id.required' => 'Id không được để trống',
            'id.exists' => 'Id không hợp lệ',
        ];
    }
}
