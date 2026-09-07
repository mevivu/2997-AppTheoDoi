<?php

namespace App\Models;

use App\Enums\Package\PackageDiscountType;
use App\Enums\Package\PackageStatus;
use App\Enums\Package\PackageType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Gói dịch vụ */
class Package extends Model
{
    use HasFactory;

    protected $table = 'packages';

    protected $fillable = [
        /* Tên gói dịch vụ */
        'name',
        /* Giá gói dịch vụ */
        'price',
        /** Số ngày */
        'days',
        /* Mô tả gói dịch vụ */
        'description',
        /* Trạng thái của gói dịch vụ */
        'status',
        /* Loại gói dịch vụ (1 tháng, 3 tháng, 6 tháng, 1 năm) */
        'type',
        'code',
        /* Loại giảm giá (none, percent, fixed) */
        'discount_type',
        /* Giá trị giảm (% hoặc số tiền) */
        'discount_value',
        /* Mã giảm giá / Voucher */
        'discount_code'
    ];
    protected $casts = [
        'status' => PackageStatus::class,
        'type' => PackageType::class,
        'discount_type' => PackageDiscountType::class,
        'discount_value' => 'float',
    ];

    /**
     * Tính giá bán sau khi áp dụng giảm giá
     */
    public function getFinalPriceAttribute(): float
    {
        $price = (float) $this->price;
        $discountValue = (float) $this->discount_value;

        if ($this->discount_type === PackageDiscountType::Percent && $discountValue > 0) {
            $discountAmount = $price * ($discountValue / 100);
            return max(0, round($price - $discountAmount));
        }

        if ($this->discount_type === PackageDiscountType::Fixed && $discountValue > 0) {
            return max(0, round($price - $discountValue));
        }

        return $price;
    }

    /**
     * Kiểm tra gói có đang được giảm giá không
     */
    public function getHasDiscountAttribute(): bool
    {
        return $this->discount_type !== null
            && $this->discount_type !== PackageDiscountType::None
            && (float) $this->discount_value > 0;
    }

    /**
     * Chuỗi hiển thị mức giảm giá trực quan (ví dụ: -20% hoặc -50.000 đ)
     */
    public function getDiscountDisplayAttribute(): string
    {
        if (!$this->has_discount) {
            return '';
        }

        if ($this->discount_type === PackageDiscountType::Percent) {
            return '-' . (int) $this->discount_value . '%';
        }

        if ($this->discount_type === PackageDiscountType::Fixed) {
            return '-' . number_format($this->discount_value, 0, ',', '.') . ' đ';
        }

        return '';
    }

    public function userPackages(): HasMany
    {
        return $this->hasMany(UserPackage::class);
    }


    public static function getTrialPackage(): ?self
    {
        return self::where('type', PackageType::Trial)->first();
    }

    public static function getNormalPackage(): ?self
    {
        return self::where('type', PackageType::Normal)->first();
    }


}
