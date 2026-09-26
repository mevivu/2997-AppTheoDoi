<?php

namespace App\Admin\Http\Requests\Video;

class CreateVideoRequest extends VideoRequest
{
    /**
     * Quy tắc validation dành riêng cho thao tác tạo mới Video
     */
    public function rules(): array
    {
        return $this->methodPost();
    }
}
