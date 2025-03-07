<?php

namespace App\Api\V1\Rules;

use App\Enums\Child\BornStatus;
use App\Models\Child;
use Illuminate\Contracts\Validation\Rule;

class ValidBornChild implements Rule
{
    protected string $errorMessage = 'ID Đứa trẻ được chọn không hợp lệ.';

    public function passes($attribute, $value): bool
    {
        $child = Child::find($value);

        if (!$child) {
            $this->errorMessage = 'Đứa trẻ không tồn tại trong hệ thống.';
            return false;
        }

        if ($child->is_born == BornStatus::Born) {
            $this->errorMessage = 'Đứa trẻ đã sinh.';
            return false;
        }

        return true;
    }

    public function message(): string
    {
        return $this->errorMessage;
    }
}
