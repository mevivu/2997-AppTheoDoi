<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use App\Enums\User\Gender;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bmi extends Model
{
    use HasFactory;

    protected $table = 'bmi_informations';

    protected $fillable = [
        /* Tuổi */
        'age',
        /* Giới tính */
        'gender',
        /* Chỉ số BMI */
        'bmi',
        /* Trạng thái */
        'status',
    ];

    protected $casts = [
        'status' => ActiveStatus::class,
        'gender' => Gender::class,
    ];
}
