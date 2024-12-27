<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use App\Enums\Permission\PermissionType;
use App\Enums\Vaccination\VaccinationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/** Lịch tiêm chủng */
class VaccinationSchedule extends Model
{
    use HasFactory;

    protected $table = 'vaccination_schedules';

    protected $fillable = [
        /** ID loại */
        'vaccination_type_id',
        /* Tên */
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
        'vaccination_status',
        /** Loại */
        'type'
    ];
    protected $casts = [
        'status' => ActiveStatus::class,
        'type' => PermissionType::class,
        'vaccination_status' => VaccinationStatus::class,
        'performed_on' => 'date',
    ];


    public function vaccinationType(): BelongsTo
    {
        return $this->belongsTo(VaccinationType::class, 'vaccination_type_id');
    }

    public function children(): BelongsToMany
    {
        return $this->belongsToMany(Child::class, 'child_vaccination_schedule', 'vaccination_schedule_id', 'child_id')
            ->withTimestamps();
    }

}
