<?php

namespace App\Models;
use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Classes extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        /** Tên lớp học */
        'name',
        /** Trạng thái của lớp học */
        'status'


    ];

    protected $casts = [
        'status'=>ActiveStatus::class

    ];


}
