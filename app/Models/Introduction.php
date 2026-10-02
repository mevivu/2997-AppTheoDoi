<?php

namespace App\Models;

use App\Admin\Support\Eloquent\Sluggable;
use App\Enums\DefaultStatus;
use App\Enums\Introduction\IntroductionSectionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Introduction extends Model
{
    use HasFactory, Sluggable;

    protected $table = 'introductions';

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
        /** Tiêu đề bài viết giới thiệu */
        'title',
        /** Đường dẫn tĩnh (Slug) */
        'slug',
        /** Phân loại khối nội dung (1: Về chúng tôi, 2: Tầm nhìn, 3: Sứ mệnh, 4: Giá trị cốt lõi, 5: Thương hiệu) */
        'section_type',
        /** Ảnh đại diện / Banner khối giới thiệu */
        'image',
        /** Biểu tượng đại diện (Icon class hoặc ảnh) */
        'icon',
        /** Đoạn trích dẫn / Tóm tắt ngắn */
        'excerpt',
        /** Nội dung chi tiết (HTML bài viết) */
        'content',
        /** Thứ tự sắp xếp hiển thị */
        'sort_order',
        /** Trạng thái bài viết (1: Xuất bản, 2: Bản nháp, 3: Thùng rác) */
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'section_type' => IntroductionSectionType::class,
        'status' => DefaultStatus::class,
        'sort_order' => 'integer',
    ];

    /**
     * Kiểm tra bài viết đã xuất bản chưa
     */
    public function isPublished(): bool
    {
        return $this->status === DefaultStatus::Published;
    }

    /**
     * Scope lọc bài viết đã xuất bản
     */
    public function scopePublished($query)
    {
        return $query->where('status', DefaultStatus::Published);
    }
}
