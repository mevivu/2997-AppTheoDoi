<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use App\Enums\Exercise\ExerciseType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    use HasFactory;

    protected $table = 'exercises';

    protected $fillable = [
        /*Tên bài tập */
        'name',
        /*Mô tả*/
        'description',
        /*Trạng thái*/
        'status',
        /*Loại bài tập*/
        'exercise_type'
    ];

    protected $casts = [
        'status' => ActiveStatus::class,
        'exercise_type' => ExerciseType::class
    ];
}
