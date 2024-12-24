<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** Năng lực*/
class Capability extends Model
{
    use HasFactory;

    protected $table = 'capabilities';


    protected $fillable = ['name', 'status'];

    protected $casts = [
        'status' => ActiveStatus::class,
    ];
}