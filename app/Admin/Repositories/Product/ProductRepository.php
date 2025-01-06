<?php

namespace App\Admin\Repositories\Product;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Product;

class ProductRepository extends EloquentRepository implements ProductRepositoryInterface
{
    public function getModel(): string
    {
        return Product::class;
    }
}
