<?php

namespace App\Models;

use App\Enums\Package\PackageStatus;
use App\Enums\Package\PackageType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** Gói dịch vụ */
class Package extends Model
{
    use HasFactory;

    protected $table = 'clinics';

    protected $fillable = [
        /* Tên gói dịch vụ */
        'name',
        /* Giá gói dịch vụ */
        'price',
        /* Mô tả gói dịch vụ */
        'description',
//        /* Ngày bắt đầu gói dịch vụ */
//        'start_date',
//        /* Ngày kết thúc gói dịch vụ */
//        'end_date',
        /* Trạng thái của gói dịch vụ */
        'status',
        /* Loại gói dịch vụ (1 tháng, 3 tháng, 6 tháng, 1 năm) */
        'type',
    ];
    protected $casts = [

        'status' => PackageStatus::class,
        'type' => PackageType::class,
    ];



}
