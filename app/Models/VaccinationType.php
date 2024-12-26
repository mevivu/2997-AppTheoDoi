<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/** Phòng khám */
class VaccinationType extends Model
{
    use HasFactory;

    protected $table = 'vaccination_types';

    protected $fillable = [
        /* Tên */
        'name',
        /* Mô tả */
        'description',
        /* Trạng thái  */
        'status',

    ];
    protected $casts = [

        'status' => ActiveStatus::class,
    ];


}
