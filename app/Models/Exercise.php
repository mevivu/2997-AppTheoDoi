<?php

namespace App\Models;

use App\Enums\Exercise\ExerciseType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    use HasFactory;

    protected $table = 'exercises';

    protected $guarded = [];

    protected $casts = [
        'exercise_type' => ExerciseType::class
    ];
}