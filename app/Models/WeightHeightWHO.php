<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use App\Enums\User\Gender;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** Phòng khám */
class WeightHeightWHO extends Model
{
    use HasFactory;

    protected $table = 'weight_height_who';

    protected $fillable = [
        /* Cân nặng của đối tượng */
        'weight',
        /* Chiều cao của đối tượng */
        'height',
        /* Tuổi của đối tượng (tính bằng năm) */
        'age',
        /* Tháng tuổi của đối tượng (nếu có) */
        'month',
        /* Giới tính của đối tượng */
        'gender',
        /* Trạng thái hoạt động của dữ liệu */
        'status',
    ];
    protected $casts = [
        'gender' => Gender::class,
        'status' => ActiveStatus::class,
    ];



}
