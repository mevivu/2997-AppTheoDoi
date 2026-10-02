<?php

namespace App\Models;

use App\Admin\Support\Eloquent\Sluggable;
use App\Enums\DefaultStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpertPost extends Model
{
    use HasFactory, Sluggable;

    protected $table = 'expert_posts';

    /**
     * Tên cột dùng để tạo slug tự động
     *
     * @var string
     */
    protected $columnSlug = 'title';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        /** Tiêu đề bài viết chuyên gia */
        'title',
        /** Đường dẫn tĩnh (Slug) */
        'slug',
        /** ID Chuyên gia / Bác sĩ biên soạn hoặc kiểm duyệt */
        'expert_id',
        /** ID Chuyên mục chuyên đề */
        'category_id',
        /** ID Nhóm độ tuổi phù hợp (0-2 tuổi, 2-4 tuổi...) */
        'age_group_id',
        /** Ảnh minh họa bìa bài viết */
        'image',
        /** Thời lượng ước tính đọc bài (VD: "3 phút đọc") */
        'reading_time',
        /** Mô tả tóm tắt nội dung bài viết */
        'excerpt',
        /** Lời khuyên nổi bật / Trích dẫn cô đọng từ Bác sĩ */
        'expert_quote',
        /** Nội dung chi tiết bài viết (HTML / CKEditor) */
        'content',
        /** Đánh dấu bài viết nổi bật ghim lên đầu */
        'is_featured',
        /** Số lượt xem bài viết */
        'views',
        /** Thứ tự sắp xếp hiển thị */
        'sort_order',
        /** Trạng thái xuất bản (1: Đã xuất bản, 2: Bản nháp, 3: Thùng rác) */
        'status',
        /** Thời gian phát hành bài viết */
        'posted_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_featured' => 'integer',
        'views' => 'integer',
        'sort_order' => 'integer',
        'status' => DefaultStatus::class,
        'posted_at' => 'datetime',
    ];

    /**
     * Thông tin chuyên gia / Bác sĩ biên soạn
     */
    public function expert(): BelongsTo
    {
        return $this->belongsTo(Expert::class, 'expert_id');
    }

    /**
     * Chuyên mục chuyên đề của bài viết
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpertCategory::class, 'category_id');
    }

    /**
     * Nhóm độ tuổi phù hợp
     */
    public function ageGroup(): BelongsTo
    {
        return $this->belongsTo(AgeGroup::class, 'age_group_id');
    }

    /**
     * Scope lọc bài viết đã xuất bản
     */
    public function scopePublished($query)
    {
        return $query->where('status', DefaultStatus::Published);
    }

    /**
     * Scope lọc bài viết nổi bật
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', 1);
    }

    /**
     * Kiểm tra bài viết đang ở trạng thái xuất bản
     */
    public function isPublished(): bool
    {
        return $this->status === DefaultStatus::Published;
    }
}
