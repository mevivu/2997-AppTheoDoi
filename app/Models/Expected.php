<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expected extends Model
{
    use HasFactory;
    protected $table = 'expecteds';
    protected $fillable = [
        'age',
        'height_expected',
        'weight_expected',
        'status',
    ];
    protected $casts = [
        'status' => ActiveStatus::class
    ];
}