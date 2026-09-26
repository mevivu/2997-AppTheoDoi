<?php

namespace App\Admin\Http\Requests\Video;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\ActiveStatus;
use App\Enums\Video\VideoAccessType;
use Illuminate\Validation\Rules\Enum;

class VideoRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'video_category_id' => ['required', 'exists:App\Models\VideoCategory,id'],
            'title' => ['required', 'string', 'max:300'],
            'description' => ['nullable', 'string'],
            'video_url' => [
                'required',
                'url',
                'regex:/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be)\/.+$/',
            ],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'duration_seconds' => ['nullable', 'integer', 'min:0'],
            'access_type' => ['required', new Enum(VideoAccessType::class)],
            'is_preview' => ['nullable'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', new Enum(ActiveStatus::class)],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\Video,id'],
            'video_category_id' => ['required', 'exists:App\Models\VideoCategory,id'],
            'title' => ['required', 'string', 'max:300'],
            'description' => ['nullable', 'string'],
            'video_url' => [
                'required',
                'url',
                'regex:/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be)\/.+$/',
            ],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'duration_seconds' => ['nullable', 'integer', 'min:0'],
            'access_type' => ['required', new Enum(VideoAccessType::class)],
            'is_preview' => ['nullable'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', new Enum(ActiveStatus::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'video_category_id.required' => 'Vui lòng chọn danh mục video',
            'title.required' => 'Vui lòng nhập tiêu đề video',
            'title.max' => 'Tiêu đề video không được vượt quá 300 ký tự',
            'video_url.required' => 'Vui lòng nhập đường dẫn video YouTube',
            'video_url.url' => 'Đường dẫn video không hợp lệ',
            'video_url.regex' => 'Đường dẫn video phải là link YouTube (youtube.com hoặc youtu.be)',
            'thumbnail.image' => 'Thumbnail phải là tệp hình ảnh',
            'access_type.required' => 'Vui lòng chọn quyền truy cập',
            'status.required' => 'Vui lòng chọn trạng thái',
        ];
    }
}
