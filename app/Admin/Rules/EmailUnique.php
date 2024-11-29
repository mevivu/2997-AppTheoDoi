<?php

namespace App\Admin\Rules;

use App\AES\AESHelper;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class EmailUnique implements Rule
{
    protected mixed $ignoreId;

    public function __construct($ignoreId = null)
    {
        $this->ignoreId = $ignoreId;

    }


    public function passes($attribute, $value): bool
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $query = DB::table('users')->where('email', AESHelper::decrypt($value));
        if ($this->ignoreId) {
            $query->where('id', '<>', $this->ignoreId);
        }

        return $query->count() === 0;
    }

    public function message(): string
    {
        return 'Email này đã được đăng ký hoặc không hợp lệ.';
    }
}
