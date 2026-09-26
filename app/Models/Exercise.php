<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use App\Enums\Exercise\ExerciseDifficulty;
use App\Enums\Exercise\ExerciseType;
use App\Enums\Video\VideoAccessType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Mô hình Exercise
 *
 * Quản lý các bài tập bao gồm bài tập thể chất/sức mạnh (hệ thống cũ) và bài tập giáo dục theo chủ đề PQ, IQ, EQ, AQ, Thai giáo (mở rộng mới).
 */
class Exercise extends Model
{
    use HasFactory;

    protected $table = 'exercises';

    protected $fillable = [
        /** Tên bài tập */
        'name',
        /** Mô tả ngắn gọn */
        'description',
        /** Trạng thái hoạt động */
        'status',
        /** Phân loại bài tập cũ: physical (Thể chất), power (Sức mạnh) */
        'exercise_type',
        /** ID danh mục bài tập giáo dục (PQ, IQ, EQ, AQ, Thai giáo) */
        'exercise_category_id',
        /** Nội dung hướng dẫn chi tiết các bước thực hiện */
        'content',
        /** Mức độ khó: easy, medium, hard, assisted */
        'difficulty',
        /** Tần suất tập luyện đề xuất */
        'frequency',
        /** Lợi ích phát triển mang lại cho trẻ */
        'benefit',
        /** Dụng cụ cần chuẩn bị khi tập */
        'tools',
        /** Quyền truy cập: free (Miễn phí), vip (Gói VIP) */
        'access_type',
        /** Lượt đã thực hiện tập luyện */
        'practice_count',
        /** Thứ tự sắp xếp hiển thị */
        'sort_order',
    ];

    protected $casts = [
        'status' => ActiveStatus::class,
        'exercise_type' => ExerciseType::class,
        'exercise_category_id' => 'integer',
        'difficulty' => ExerciseDifficulty::class,
        'access_type' => VideoAccessType::class,
        'practice_count' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Danh mục giáo dục liên kết
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ExerciseCategory::class, 'exercise_category_id');
    }

    /**
     * Danh sách media minh họa (hình ảnh, video) kèm theo bài tập
     */
    public function media(): HasMany
    {
        return $this->hasMany(ExerciseMedia::class, 'exercise_id')->orderBy('sort_order');
    }

    /**
     * Phạm vi truy vấn bài tập đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('status', ActiveStatus::Active);
    }

    /**
     * Phạm vi truy vấn bài tập miễn phí
     */
    public function scopeFree($query)
    {
        return $query->where('access_type', VideoAccessType::FREE);
    }

    /**
     * Phạm vi truy vấn bài tập dành cho tài khoản VIP
     */
    public function scopeVip($query)
    {
        return $query->where('access_type', VideoAccessType::VIP);
    }
}
