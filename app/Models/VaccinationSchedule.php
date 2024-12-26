<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use App\Enums\Vaccination\VaccinationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Lịch tiêm chủng */
class VaccinationSchedule extends Model
{
    use HasFactory;

    protected $table = 'vaccination_schedules';

    protected $fillable = [
        /** Child ID */
        'child_id',
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
        /* Trạng thái tiêm chủng */
        'vaccination_status'
    ];
    protected $casts = [
        'status' => ActiveStatus::class,
        'vaccination_status' => VaccinationStatus::class,
        'performed_on' => 'date',
    ];

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class, 'child_id');
    }

}
