<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**Thương hiệu*/
class Brand extends Model
{
    use HasFactory;

    protected $table = 'brands';


    protected $fillable = [
        /** Tên */
        'name',
        /**Mô tả*/
        'description',
        /** Đất nước */
        'country',
        /** Trạng thái */
        'status'
    ];

    protected $casts = [
        'status' => ActiveStatus::class
    ];

}
