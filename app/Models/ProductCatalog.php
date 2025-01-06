<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'product_catalog_product',
            'product_catalog_id',
            'product_id'
        );
    }

}
