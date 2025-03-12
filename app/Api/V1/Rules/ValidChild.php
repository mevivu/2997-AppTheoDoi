<?php

namespace App\Api\V1\Rules;

use App\Enums\Child\BornStatus;
use App\Models\Child;
use Illuminate\Contracts\Validation\Rule;
class ValidChild implements Rule
{
    protected string $errorMessage = 'ID Đứa trẻ được chọn không hợp lệ.';

    public function passes($attribute, $value): bool
    {
        $child_id = request()->get('child_id') ?  request()->get('child_id') : $value;
        $child = Child::find($child_id);

        if (!$child) {
            $this->errorMessage = 'Đứa trẻ không tồn tại trong hệ thống.';
            return false;
        }

        if ($child->is_born == BornStatus::Unborn) {
            $this->errorMessage = 'Đứa trẻ này chưa được sinh.';
            return false;
        }

        return true;
    }

    public function message(): string
    {
        return $this->errorMessage;
    }
}
