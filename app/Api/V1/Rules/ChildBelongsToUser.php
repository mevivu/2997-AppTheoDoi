<?php

namespace App\Api\V1\Rules;

use App\Models\Child;
use Illuminate\Contracts\Validation\Rule;

class ChildBelongsToUser implements Rule
{
    protected string $errorMessage = 'Trẻ không tồn tại hoặc không thuộc quyền quản lý của bạn.';

    public function passes($attribute, $value): bool
    {
        $userId = auth('api')->id() ?? auth()->id();
        if (!$userId) {
            $this->errorMessage = 'Bạn chưa đăng nhập.';
            return false;
        }

        if (empty($value)) {
            return false;
        }

        $exists = Child::query()
            ->where('id', $value)
            ->where('user_id', $userId)
            ->exists();

        if (!$exists) {
            $this->errorMessage = 'Trẻ không tồn tại hoặc không thuộc quyền quản lý của bạn.';
            return false;
        }

        return true;
    }

    public function message(): string
    {
        return $this->errorMessage;
    }
}
