<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\URL;

/**
 * App\Models\ChildEvaluationAttachment
 *
 * Model quản lý tệp đính kèm / ảnh chụp học bạ của trẻ
 *
 * @property int $id
 * @property int $child_evaluation_id ID của bảng đánh giá học kỳ
 * @property string $disk Disk lưu trữ file (local, public, s3)
 * @property string $file_path Đường dẫn lưu file trên disk
 * @property string $original_name Tên file gốc lúc tải lên
 * @property string|null $mime_type Định dạng MIME của file
 * @property int|null $size_bytes Dung lượng file (bytes)
 * @property int|null $width Chiều rộng ảnh (pixels)
 * @property int|null $height Chiều cao ảnh (pixels)
 * @property int $sort_order Thứ tự sắp xếp hiển thị
 * @property int|null $uploaded_by ID người dùng tải ảnh lên
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string|null $url
 * @property-read \App\Models\ChildEvaluation|null $childEvaluation
 * @property-read \App\Models\User|null $uploader
 */
class ChildEvaluationAttachment extends Model
{
    use HasFactory;

    /**
     * Tên bảng trong cơ sở dữ liệu
     *
     * @var string
     */
    protected $table = 'child_evaluation_attachments';

    /**
     * The attributes that are mass assignable.
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
     * The attributes that should be cast.
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
     * The accessors to append to the model's array form.
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
     * Đường dẫn truy cập ảnh học bạ: link CDN Cloudflare R2 trực tiếp hoặc signed URL
     */
    public function getUrlAttribute(): ?string
    {
        if (!$this->exists) {
            return null;
        }

        // Nếu tệp lưu trên Cloudflare R2, trả về trực tiếp link CDN R2
        if ($this->disk === 'r2') {
            $baseUrl = rtrim(config('filesystems.disks.r2.url') ?: env('CLOUDFLARE_R2_PUBLIC_URL', ''), '/');
            if ($baseUrl && !empty($this->file_path)) {
                return $baseUrl . '/' . ltrim($this->file_path, '/');
            }
        }

        try {
            return URL::temporarySignedRoute(
                'api.v1.childEvaluation.attachments.file',
                now()->addMinutes(30),
                ['attachmentId' => $this->id]
            );
        } catch (\Throwable $e) {
            return null;
        }
    }
}
