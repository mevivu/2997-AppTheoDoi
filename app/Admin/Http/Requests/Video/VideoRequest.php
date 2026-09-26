<?php

namespace App\Admin\Http\Requests\Video;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\ActiveStatus;
use App\Enums\Video\VideoAccessType;
use App\Enums\Video\VideoType;
use Illuminate\Validation\Rules\Enum;

class VideoRequest extends BaseRequest
{
    /**
     * Quy tắc validation khi tạo mới Video (POST)
     */
    protected function methodPost(): array
    {
        $videoTypeInput = $this->input('video_type');
        $isVideoR2 = $videoTypeInput === VideoType::R2->value || $videoTypeInput === 'r2';

        $rules = [
            'video_category_id' => ['required', 'exists:App\Models\VideoCategory,id'],
            'video_type' => ['required', new Enum(VideoType::class)],
            'title' => ['required', 'string', 'max:300'],
            'description' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'duration_seconds' => ['nullable', 'integer', 'min:0'],
            'access_type' => ['required', new Enum(VideoAccessType::class)],
            'is_preview' => ['nullable'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', new Enum(ActiveStatus::class)],
        ];

        $videoRule = [
            'file',
            'max:204800', // 200MB (200 * 1024 KB)
            function ($attribute, $value, $fail) {
                if (!$value instanceof \Illuminate\Http\UploadedFile || !$value->isValid()) {
                    return;
                }
                $allowed = ['mp4', 'mov', 'webm', 'mkv', 'avi', 'm4v'];
                $clientExt = strtolower($value->getClientOriginalExtension());
                $guessExt = strtolower($value->guessExtension() ?? '');

                if (!in_array($clientExt, $allowed) && !in_array($guessExt, $allowed)) {
                    $fail(__('Tệp video phải có định dạng: mp4, mov, webm, mkv, avi.'));
                }
            },
        ];

        if ($isVideoR2) {
            $rules['video_url'] = ['nullable', 'string'];
            $rules['video_file'] = array_merge(['required'], $videoRule);
        } else {
            $rules['video_url'] = [
                'required',
                'url',
                'regex:/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be)\/.+$/i',
            ];
            $rules['video_file'] = array_merge(['nullable'], $videoRule);
        }

        return $rules;
    }

    /**
     * Quy tắc validation khi cập nhật Video (PUT)
     */
    protected function methodPut(): array
    {
        $videoTypeInput = $this->input('video_type');
        $isVideoR2 = $videoTypeInput === VideoType::R2->value || $videoTypeInput === 'r2';

        $rules = [
            'id' => ['required', 'exists:App\Models\Video,id'],
            'video_category_id' => ['required', 'exists:App\Models\VideoCategory,id'],
            'video_type' => ['required', new Enum(VideoType::class)],
            'title' => ['required', 'string', 'max:300'],
            'description' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'duration_seconds' => ['nullable', 'integer', 'min:0'],
            'access_type' => ['required', new Enum(VideoAccessType::class)],
            'is_preview' => ['nullable'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', new Enum(ActiveStatus::class)],
        ];

        $videoRule = [
            'file',
            'max:204800', // 200MB
            function ($attribute, $value, $fail) {
                if (!$value instanceof \Illuminate\Http\UploadedFile || !$value->isValid()) {
                    return;
                }
                $allowed = ['mp4', 'mov', 'webm', 'mkv', 'avi', 'm4v'];
                $clientExt = strtolower($value->getClientOriginalExtension());
                $guessExt = strtolower($value->guessExtension() ?? '');

                if (!in_array($clientExt, $allowed) && !in_array($guessExt, $allowed)) {
                    $fail(__('Tệp video phải có định dạng: mp4, mov, webm, mkv, avi.'));
                }
            },
        ];

        if ($isVideoR2) {
            $rules['video_url'] = ['nullable', 'string'];
            // Khi cập nhật video R2: file là optional (nếu không chọn file mới thì giữ file cũ)
            $rules['video_file'] = array_merge(['nullable'], $videoRule);
        } else {
            $rules['video_url'] = [
                'required',
                'url',
                'regex:/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be)\/.+$/i',
            ];
            $rules['video_file'] = array_merge(['nullable'], $videoRule);
        }

        return $rules;
    }

    /**
     * Thông báo lỗi tùy chỉnh (tiếng Việt chi tiết)
     */
    public function messages(): array
    {
        return [
            'video_category_id.required' => __('Vui lòng chọn danh mục video.'),
            'video_category_id.exists' => __('Danh mục video được chọn không tồn tại.'),
            'video_type.required' => __('Vui lòng chọn hình thức video (YouTube hoặc Cloudflare R2).'),
            'video_type.enum' => __('Hình thức video không hợp lệ.'),
            'title.required' => __('Vui lòng nhập tiêu đề video.'),
            'title.max' => __('Tiêu đề video không được vượt quá 300 ký tự.'),
            'video_url.required' => __('Vui lòng nhập đường dẫn video YouTube.'),
            'video_url.url' => __('Đường dẫn video không đúng định dạng liên kết URL.'),
            'video_url.regex' => __('Đường dẫn video phải là liên kết hợp lệ từ YouTube (youtube.com hoặc youtu.be).'),
            'video_file.required' => __('Vui lòng chọn tệp video để tải lên Cloudflare R2.'),
            'video_file.file' => __('Tệp tải lên không hợp lệ.'),
            'video_file.mimes' => __('Tệp video phải có định dạng: mp4, mov, webm, mkv, avi.'),
            'video_file.max' => __('Dung lượng tệp video vượt quá giới hạn 200MB.'),
            'video_file.uploaded' => __('Không thể tải tệp lên hoặc dung lượng tệp vượt quá cấu hình máy chủ.'),
            'thumbnail.image' => __('Ảnh thu nhỏ phải là tệp hình ảnh.'),
            'thumbnail.mimes' => __('Ảnh thu nhỏ phải có định dạng: jpeg, png, jpg, webp.'),
            'thumbnail.max' => __('Dung lượng ảnh thu nhỏ không được vượt quá 2MB.'),
            'duration_seconds.integer' => __('Thời lượng video phải là số nguyên (giây).'),
            'duration_seconds.min' => __('Thời lượng video không được nhỏ hơn 0.'),
            'access_type.required' => __('Vui lòng chọn đối tượng được xem video (Miễn phí hoặc Gói VIP).'),
            'status.required' => __('Vui lòng chọn trạng thái hiển thị.'),
        ];
    }

    /**
     * Tên hiển thị của các thuộc tính
     */
    public function attributes(): array
    {
        return [
            'video_category_id' => __('Danh mục video'),
            'video_type' => __('Hình thức cung cấp video'),
            'title' => __('Tiêu đề video'),
            'description' => __('Mô tả video'),
            'video_url' => __('Đường dẫn YouTube'),
            'video_file' => __('Tệp video tải lên'),
            'thumbnail' => __('Ảnh thu nhỏ'),
            'duration_seconds' => __('Thời lượng video'),
            'access_type' => __('Quyền xem video'),
            'is_preview' => __('Cho phép xem thử'),
            'sort_order' => __('Thứ tự sắp xếp'),
            'status' => __('Trạng thái hiển thị'),
        ];
    }
}
