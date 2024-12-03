<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Phòng khám */
class Clinic extends Model
{
    use HasFactory;

    protected $table = 'clinics';

    protected $fillable = [
        /* Tên phòng khám */
        'name',
        /* Địa chỉ phòng khám */
        'address',
        /* Số điện thoại của phòng khám */
        'hotline',
        /* Giờ mở cửa */
        'opening_time',
        /* Giờ đóng cửa */
        'closing_time',
        /* Trạng thái phòng khám */
        'status',
        /* ID loại phòng khám */
        'clinic_type_id',
        /* ID tỉnh */
        'province_id',
        /* ID quận */
        'district_id',
        /* ID phường */
        'ward_id',
    ];
    protected $casts = [

        'status' => ActiveStatus::class,
    ];

    public function clinicType(): BelongsTo
    {
        return $this->belongsTo(ClinicType::class, 'clinic_type_id');
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class, 'ward_id');
    }



}
