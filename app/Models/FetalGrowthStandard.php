<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Tiêu chuẩn chiều dài, cân nặng thai nhi theo tuần (Vinmec / WHO)
 */
class FetalGrowthStandard extends Model
{
    use HasFactory;

    protected $table = 'fetal_growth_standards';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        /** Tuần thai (8 - 42) */
        'week',
        /** Chiều dài chuẩn của thai nhi (cm) */
        'length',
        /** Cân nặng chuẩn của thai nhi (g) */
        'weight',
        /** Chu vi đầu thai nhi (cm - tùy chọn) */
        'head_circumference',
        /** Mô tả / ghi chú bổ sung */
        'description',
        /** Trạng thái kích hoạt (1: Hoạt động, 2: Không hoạt động) */
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'week' => 'integer',
        'length' => 'float',
        'weight' => 'float',
        'head_circumference' => 'float',
        'status' => ActiveStatus::class,
    ];
}
