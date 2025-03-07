<?php

namespace App\Admin\Rules;

use App\AES\AESHelper;
use App\Models\ProductCatalog;
use Illuminate\Contracts\Validation\Rule;

class ProductCatalogNameUnique implements Rule
{
    protected mixed $ignoreId;

    public function __construct($ignoreId = null)
    {
        $this->ignoreId = $ignoreId;

    }

    public function passes($attribute, $value): bool
    {
        $query = ProductCatalog::where('name', $value);
        if ($this->ignoreId) {
            $query->where('id', '<>', $this->ignoreId);
        }

        return $query->count() === 0;
    }

    public function message(): string
    {
        return 'Danh mục này đã tồn tại.';
    }
}
