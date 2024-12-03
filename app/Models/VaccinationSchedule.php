<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** Lịch tiêm chủng */
class VaccinationSchedule extends Model
{
    use HasFactory;

    protected $table = 'vaccination_schedules';

    protected $fillable = [
        /* Tên phòng khám */
        'name',
        /** Mô tả */
        'description',
        /** Hình ảnh minh hoạ */
        'image',
        /** Ngày tiêm thực tế */
        'performed_on',
        /* Trạng thái */
        'status',
    ];
    protected $casts = [
        'status' => ActiveStatus::class,
        'performed_on' => 'date',
    ];

}
