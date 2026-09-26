<?php

namespace App\Admin\Http\Requests\Video;

class UpdateVideoRequest extends VideoRequest
{
    /**
     * Quy tắc validation dành riêng cho thao tác cập nhật Video
     */
    public function rules(): array
    {
        return $this->methodPut();
    }
}
