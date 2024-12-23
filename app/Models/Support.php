<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use App\Enums\Support\SupportType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Support extends Model
{
    use HasFactory;

    protected $table = "supports";

    protected $fillable = [
        /*Tiêu đề*/
        'title',
        /*Nội dung*/
        'content',
        /*Loại*/
        'type',
        /*Trạng thái*/
        'status',
    ];

    protected $casts = [
        'status' => ActiveStatus::class,
        'type' => SupportType::class,
    ];
}