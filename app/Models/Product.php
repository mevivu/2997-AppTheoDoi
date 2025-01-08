<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**Sản phẩm*/
class Product extends Model
{
    use HasFactory;

    protected $table = 'products';


    protected $fillable = [
        /** Tên sản phẩm */
        'name',
        /** Danh mục sản phẩm */
        'product_catalog_id',
        /** Thương hiệu */
        'brand_id',
        /** Trạng thái */
        'status'
    ];

    protected $casts = [
        'status' => ActiveStatus::class
    ];

    public function productCatalogs(): BelongsToMany
    {
        return $this->belongsToMany(ProductCatalog::class,
            'product_catalog_product',
            'product_id');
    }


    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

}
