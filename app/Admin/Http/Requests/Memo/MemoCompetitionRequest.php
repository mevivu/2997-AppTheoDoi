<?php

namespace App\Admin\Http\Requests\Memo;

use App\Admin\Http\Requests\BaseRequest;

class MemoCompetitionRequest extends BaseRequest
{
    /**
     * Quy tắc xác thực khi tạo mới giải đấu (POST)
     */
    protected function methodPost(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after:start_at'],
            'memo_age_config_id' => ['required', 'integer', 'exists:memo_age_configs,id'],
            'theme_ids' => ['required', 'array', 'min:4', 'max:4'],
            'theme_ids.*' => ['required', 'integer', 'exists:memo_themes,id'],
            'max_attempts' => ['nullable', 'integer', 'min:0', 'max:100'],
            'status' => ['required', 'string', 'in:draft,upcoming,active,ended,cancelled'],
            'banner_image' => ['nullable', 'image', 'max:4096'],
            'description' => ['nullable', 'string'],
        ];
    }

    /**
     * Quy tắc xác thực khi cập nhật giải đấu (PUT)
     */
    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:memo_competitions,id'],
            'name' => ['required', 'string', 'max:255'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after:start_at'],
            'memo_age_config_id' => ['required', 'integer', 'exists:memo_age_configs,id'],
            'theme_ids' => ['required', 'array', 'min:4', 'max:4'],
            'theme_ids.*' => ['required', 'integer', 'exists:memo_themes,id'],
            'max_attempts' => ['nullable', 'integer', 'min:0', 'max:100'],
            'status' => ['required', 'string', 'in:draft,upcoming,active,ended,cancelled'],
            'banner_image' => ['nullable', 'image', 'max:4096'],
            'description' => ['nullable', 'string'],
        ];
    }

    /**
     * Thông báo lỗi xác thực tiếng Việt
     */
    public function messages(): array
    {
        return [
            'id.required' => 'Thiếu ID giải đấu cần cập nhật.',
            'id.exists' => 'Giải đấu không tồn tại trong hệ thống.',
            'name.required' => 'Vui lòng nhập tên giải đấu.',
            'name.max' => 'Tên giải đấu không được vượt quá 255 ký tự.',
            'start_at.required' => 'Vui lòng chọn thời gian bắt đầu giải đấu.',
            'start_at.date' => 'Thời gian bắt đầu không hợp lệ.',
            'end_at.required' => 'Vui lòng chọn thời gian kết thúc giải đấu.',
            'end_at.date' => 'Thời gian kết thúc không hợp lệ.',
            'end_at.after' => 'Thời gian kết thúc phải diễn ra sau thời gian bắt đầu.',
            'memo_age_config_id.required' => 'Vui lòng chọn cấu hình lưới bài thi (5×6).',
            'memo_age_config_id.exists' => 'Cấu hình lưới bài thi không tồn tại.',
            'theme_ids.required' => 'Vui lòng chọn đủ 4 chủ đề cho 4 ván thi.',
            'theme_ids.min' => 'Giải đấu tiêu chuẩn yêu cầu chọn đúng 4 chủ đề.',
            'theme_ids.max' => 'Giải đấu tiêu chuẩn yêu cầu chọn đúng 4 chủ đề.',
            'theme_ids.*.required' => 'Mỗi ván thi bắt buộc phải chọn 1 chủ đề.',
            'theme_ids.*.exists' => 'Chủ đề đã chọn không tồn tại trong hệ thống.',
            'max_attempts.integer' => 'Số lượt thi tối đa phải là số nguyên.',
            'max_attempts.min' => 'Số lượt thi tối thiểu là 0 (không giới hạn).',
            'status.required' => 'Vui lòng chọn trạng thái mở giải.',
            'status.in' => 'Trạng thái giải đấu không hợp lệ.',
            'banner_image.image' => 'File tải lên phải là định dạng hình ảnh.',
            'banner_image.max' => 'Dung lượng ảnh banner tối đa là 4MB.',
        ];
    }
}
