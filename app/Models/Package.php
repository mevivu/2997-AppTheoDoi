<?php

namespace App\Models;

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
        /* Mô tả gói dịch vụ */
        'description',
        /* Trạng thái của gói dịch vụ */
        'status',
        /* Loại gói dịch vụ (1 tháng, 3 tháng, 6 tháng, 1 năm) */
        'type',
    ];
    protected $casts = [

        'status' => PackageStatus::class,
        'type' => PackageType::class,
    ];

    public function userPackages(): HasMany
    {
        return $this->hasMany(UserPackage::class);
    }

    public function features(): HasMany
    {
        return $this->hasMany(Feature::class);
    }

    public static function getTrialPackage(): ?self
    {
        return self::where('type', PackageType::Trial)->first();
    }



}
