<?php

namespace App\Admin\Rules;

use App\AES\AESHelper;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class PhoneUnique implements Rule
{
    protected mixed $ignoreId;

    public function __construct($ignoreId = null)
    {
        $this->ignoreId = $ignoreId;

    }


    public function passes($attribute, $value): bool
    {
        if (!preg_match('/^((09|03|07|08|05)+([0-9]{8})\b)/', $value)) {
            return false;
        }
        $query = DB::table('users')->where('phone', AESHelper::decrypt($value));
        if ($this->ignoreId) {
            $query->where('id', '<>', $this->ignoreId);
        }
        return $query->count() === 0;
    }

    public function message(): string
    {
        return 'Số điện thoại này đã được đăng ký hoặc không hợp lệ.';
    }
}
