<?php

namespace App\Models;

use App\Admin\Support\Eloquent\Sluggable;
use App\Enums\ActiveStatus;
use App\Enums\Lesson\LessonAccessType;
use App\Enums\Lesson\LessonDifficulty;
use App\Traits\ImageSystem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Mô hình Lesson (Bài học giáo dục cho trẻ)
 *
 * Quản lý bài học giáo dục thuộc các danh mục con, hỗ trợ đính kèm video hướng dẫn (YouTube hoặc Cloudflare R2).
 * Phục vụ phân luồng nội dung theo độ tuổi, trụ cột giáo dục và phân quyền truy cập (Miễn phí / VIP).
 */
class Lesson extends Model
{
    use HasFactory, Sluggable;

    /**
     * Bảng cơ sở dữ liệu tương ứng
     *
     * @var string
     */
    protected $table = 'lessons';

    /**
     * Các trường dữ liệu cho phép gán hàng loạt (Mass Assignment)
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'lesson_category_id', // ID danh mục bài học
        'name',               // Tên tiêu đề bài học
        'slug',               // Đường dẫn thân thiện SEO
        'image',              // Ảnh đại diện riêng bài học (nếu có)
        'description',        // Mô tả tóm tắt ngắn
        'content',            // Nội dung hướng dẫn chi tiết cho phụ huynh
        'difficulty',         // Độ khó: easy (Dễ), medium (Có trợ giúp), hard (Thử thách)
        'frequency',          // Tần suất gợi ý (ví dụ: Hàng ngày, 3 lần/tuần)
        'benefit',            // Lợi ích phát triển mang lại cho bé
        'tools',              // Dụng cụ / học cụ cần chuẩn bị
        'access_type',        // Phân quyền: free (Miễn phí), vip (Gói VIP)
        'view_count',         // Lượt xem bài học
        'sort_order',         // Thứ tự hiển thị
        'status',             // Trạng thái hoạt động
    ];

    /**
     * Ép kiểu dữ liệu tự động cho các thuộc tính
     *
     * @var array<string, string>
     */
    protected $casts = [
        'lesson_category_id' => 'integer',
        'difficulty' => LessonDifficulty::class,
        'access_type' => LessonAccessType::class,
        'view_count' => 'integer',
        'sort_order' => 'integer',
        'status' => ActiveStatus::class,
    ];

    /**
     * Các thuộc tính ảo bổ sung vào mảng/JSON khi serialize
     *
     * @var array<int, string>
     */
    protected $appends = ['video_count', 'first_video_thumbnail', 'thumbnail_url'];

    /**
     * Liên kết: Danh mục bài học mà bài học này thuộc về
     *
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(LessonCategory::class, 'lesson_category_id');
    }

    /**
     * Liên kết: Video chính của bài học (mỗi bài học có 1 video chính sắp xếp đầu tiên)
     *
     * @return HasOne
     */
    public function video(): HasOne
    {
        return $this->hasOne(LessonVideo::class, 'lesson_id')->orderBy('sort_order', 'asc');
    }

    /**
     * Liên kết: Danh sách tất cả video đính kèm trong bài học
     *
     * @return HasMany
     */
    public function videos(): HasMany
    {
        return $this->hasMany(LessonVideo::class, 'lesson_id')->orderBy('sort_order', 'asc');
    }

    /**
     * Liên kết: Video đầu tiên của bài học theo thứ tự hiển thị
     *
     * @return HasOne
     */
    public function firstVideo(): HasOne
    {
        return $this->hasOne(LessonVideo::class, 'lesson_id')->orderBy('sort_order', 'asc');
    }

    /**
     * Thuộc tính ảo: Tổng số lượng video đính kèm trong bài học
     *
     * @return int
     */
    public function getVideoCountAttribute(): int
    {
        return $this->videos()->count();
    }

    /**
     * Thuộc tính ảo: URL ảnh bìa / thumbnail của bài học
     * Ưu tiên: Ảnh bài học -> Ảnh thumbnail video đầu tiên -> Ảnh mặc định hệ thống (ImageSystem::DEFAULT_IMAGE)
     *
     * @return string
     */
    public function getThumbnailUrlAttribute(): string
    {
        if (!empty($this->image)) {
            return asset($this->image);
        }
        $first = $this->videos->first();
        if ($first) {
            return $first->thumbnail_url;
        }
        return asset(ImageSystem::DEFAULT_IMAGE);
    }

    /**
     * Thuộc tính ảo: URL ảnh bìa của video đầu tiên
     * Trả về ảnh thumbnail của video đầu tiên hoặc ảnh mặc định từ ImageSystem
     *
     * @return string
     */
    public function getFirstVideoThumbnailAttribute(): string
    {
        $first = $this->videos->first();
        if ($first) {
            return $first->thumbnail_url;
        }
        return asset(ImageSystem::DEFAULT_IMAGE);
    }

    /**
     * Kiểm tra bài học có phải dạng miễn phí (Free) hay không
     *
     * @return bool
     */
    public function isFree(): bool
    {
        return ($this->access_type ?? LessonAccessType::Free) === LessonAccessType::Free;
    }

    /**
     * Kiểm tra bài học có yêu cầu quyền VIP hay không
     *
     * @return bool
     */
    public function isVip(): bool
    {
        return ($this->access_type ?? null) === LessonAccessType::Vip;
    }

    /**
     * Phạm vi truy vấn (Scope): Lọc các bài học đang ở trạng thái kích hoạt (Active)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', ActiveStatus::Active->value);
    }

    /**
     * Phạm vi truy vấn (Scope): Lọc các bài học miễn phí
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFree($query)
    {
        return $query->where('access_type', LessonAccessType::Free->value);
    }

    /**
     * Phạm vi truy vấn (Scope): Lọc các bài học dành riêng cho tài khoản VIP
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVip($query)
    {
        return $query->where('access_type', LessonAccessType::Vip->value);
    }

    /**
     * Phạm vi truy vấn (Scope): Lọc bài học theo danh mục
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $categoryId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('lesson_category_id', $categoryId);
    }

    /**
     * Phạm vi truy vấn (Scope): Lọc bài học theo mức độ khó (Dễ / Có trợ giúp / Thử thách)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param LessonDifficulty|string $difficulty
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByDifficulty($query, LessonDifficulty|string $difficulty)
    {
        $val = $difficulty instanceof LessonDifficulty ? $difficulty->value : $difficulty;
        return $query->where('difficulty', $val);
    }
}
