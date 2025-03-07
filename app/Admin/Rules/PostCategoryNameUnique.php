<?php

namespace App\Admin\Rules;

use App\AES\AESHelper;
use App\Models\PostCategory;
use Illuminate\Contracts\Validation\Rule;

class PostCategoryNameUnique implements Rule
{
    protected mixed $ignoreId;

    public function __construct($ignoreId = null)
    {
        $this->ignoreId = $ignoreId;

    }

    public function passes($attribute, $value): bool
    {
        $query = PostCategory::where('name', $value);
        if ($this->ignoreId) {
            $query->where('id', '<>', $this->ignoreId);
        }

        return $query->count() === 0;
    }

    public function message(): string
    {
        return 'Chuyên mục này đã tồn tại.';
    }
}
