<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Thống kê Lượt Sử Dụng Chức Năng
 *
 * Ghi nhận nhật ký tương tác của phụ huynh/trẻ em đối với các tính năng (Đánh giá & Tiện ích)
 */
class FeatureUsage extends Model
{
    use HasFactory;

    protected $table = 'feature_usages';

    public $timestamps = true;

    protected $fillable = [
        /** ID của người dùng (phụ huynh) */
        'user_id',
        /** ID của bé đang chọn */
        'child_id',
        /** Mã định danh chức năng (vd: iq, eq, aq, pq, gpa, vaccine,...) */
        'feature_code',
        /** Tên hiển thị chức năng (vd: Chỉ số thông minh (IQ)) */
        'feature_name',
        /** Nhóm phân loại: evaluation (Đánh giá toàn diện), utility (Tiện ích) */
        'category',
        /** Hành động tương tác: view (truy cập), click, evaluate,... */
        'action',
        /** Dữ liệu đính kèm mở rộng */
        'metadata',
        /** Địa chỉ IP của thiết bị gửi yêu cầu */
        'ip_address',
        /** Thông tin thiết bị / ứng dụng (User-Agent) */
        'user_agent',
    ];

    /** Ép kiểu dữ liệu */
    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Khởi tạo lifecycle model
     * Tự động bổ sung tên chức năng và danh mục từ Catalog nếu bị bỏ trống
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->feature_name) || empty($model->category)) {
                $catalog = \App\Admin\Services\FeatureStatisticsService::getFeatureCatalog();
                $meta = $catalog[$model->feature_code] ?? null;
                if (empty($model->feature_name)) {
                    $model->feature_name = $meta['name'] ?? ucfirst($model->feature_code);
                }
                if (empty($model->category)) {
                    $model->category = $meta['category'] ?? 'evaluation';
                }
            }
        });
    }

    /**
     * Người dùng (phụ huynh)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Bé được áp dụng khi sử dụng chức năng
     */
    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class, 'child_id');
    }
}
