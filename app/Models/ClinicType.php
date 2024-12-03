<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

/** Loại phòng khám */
class ClinicType extends Model
{
    use HasFactory, NodeTrait;

    protected $table = 'clinic_types';

    protected $fillable = [
        /* Tên */
        'name',
        /* Mô tả */
        'description',
        /* Trạng thái */
        'status',
    ];

    protected $casts = [
        'status' => ActiveStatus::class,
    ];
}
