<?php

namespace App\Admin\Rules;

use App\AES\AESHelper;
use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

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
        $encryptEmail = AESHelper::encrypt($value);

        $query = User::where('email', $encryptEmail);
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
