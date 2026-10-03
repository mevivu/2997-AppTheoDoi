<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\URL;

class ChildEvaluationAttachment extends Model
{
    use HasFactory;

    protected $table = 'child_evaluation_attachments';

    /**
     * Các thuộc tính có thể gán hàng loạt (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        /** ID của bảng đánh giá học kỳ */
        'child_evaluation_id',
        /** Disk lưu trữ file (local, public, s3) */
        'disk',
        /** Đường dẫn lưu file trên disk */
        'file_path',
        /** Tên file gốc lúc tải lên */
        'original_name',
        /** Định dạng MIME của file */
        'mime_type',
        /** Dung lượng file (bytes) */
        'size_bytes',
        /** Chiều rộng ảnh (pixels) */
        'width',
        /** Chiều cao ảnh (pixels) */
        'height',
        /** Thứ tự sắp xếp hiển thị */
        'sort_order',
        /** ID người dùng tải ảnh lên */
        'uploaded_by',
    ];

    /**
     * Chuyển đổi kiểu dữ liệu các trường thuộc tính.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'size_bytes' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Các trường phụ được tự động tính toán kèm theo khi serialize.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'url',
    ];

    /**
     * Bản ghi đánh giá học tập liên quan
     */
    public function childEvaluation(): BelongsTo
    {
        return $this->belongsTo(ChildEvaluation::class, 'child_evaluation_id');
    }

    /**
     * Người dùng đã thực hiện tải ảnh học bạ lên
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Đường dẫn truy cập ảnh học bạ có chữ ký bảo vệ (signed URL)
     */
    public function getUrlAttribute(): ?string
    {
        if (!$this->exists) {
            return null;
        }

        try {
            return URL::temporarySignedRoute(
                'api.v1.child-evaluations.attachments.file',
                now()->addMinutes(30),
                ['attachmentId' => $this->id]
            );
        } catch (\Throwable $e) {
            return null;
        }
    }
}
