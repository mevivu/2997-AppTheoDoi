<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** Danh mục sản phẩm */
class ProductCatalog extends Model
{
    use HasFactory;

    protected $table = 'product_catalog';


    protected $fillable = [
        /** Tên */
        'name',
        /** Trạng thái */
        'status'
    ];

    protected $casts = [
        'status' => ActiveStatus::class
    ];

}
