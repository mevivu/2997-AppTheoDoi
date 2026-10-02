<?php

namespace App\Models;

use App\Admin\Support\Eloquent\Sluggable;
use App\Enums\DefaultStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpertCategory extends Model
{
    use HasFactory, Sluggable;

    protected $table = 'expert_categories';

    /**
     * Tên cột dùng để tạo slug tự động
     *
     * @var string
     */
    protected $columnSlug = 'name';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        /** Tên chuyên mục chuyên đề (Dinh dưỡng, Chiều cao, Tâm lý...) */
        'name',
        /** Đường dẫn tĩnh (Slug) */
        'slug',
        /** Biểu tượng đại diện cho chuyên mục */
        'icon',
        /** Mô tả chi tiết phạm vi chuyên mục */
        'description',
        /** Thứ tự sắp xếp hiển thị */
        'sort_order',
        /** Trạng thái hoạt động (1: Hiển thị, 2: Bản nháp, 3: Thùng rác) */
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => DefaultStatus::class,
        'sort_order' => 'integer',
    ];

    /**
     * Danh sách bài viết thuộc chuyên mục
     */
    public function expertPosts(): HasMany
    {
        return $this->hasMany(ExpertPost::class, 'category_id');
    }

    /**
     * Scope lọc chuyên mục đã xuất bản
     */
    public function scopePublished($query)
    {
        return $query->where('status', DefaultStatus::Published);
    }

    /**
     * Kiểm tra chuyên mục đang ở trạng thái hiển thị
     */
    public function isPublished(): bool
    {
        return $this->status === DefaultStatus::Published;
    }
}
