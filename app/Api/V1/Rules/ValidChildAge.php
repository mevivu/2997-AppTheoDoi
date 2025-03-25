<?php

namespace App\Api\V1\Rules;

use App\Enums\Child\BornStatus;
use App\Enums\User\Gender;
use App\Models\Child;
use Illuminate\Contracts\Validation\Rule;

class ValidChildAge implements Rule
{
    protected string $errorMessage = 'ID Đứa trẻ được chọn không hợp lệ.';

    public function passes($attribute, $value): bool
    {
        $child = Child::find($value);

        if (!$child) {
            $this->errorMessage = 'Đứa trẻ không tồn tại trong hệ thống.';
            return false;
        }
        if($child->gender == Gender::Unknown){
            $this->errorMessage = 'Đứa trẻ chưa biết giới tính.';
            return false;
        }

        $user = $child->user;
        if ($user->father_height == null || $user->mother_height == null) {
            $this->errorMessage = 'Chưa có chiều cao của Bố hoặc mẹ.';
            return false;
        }


        if ($child->is_born == BornStatus::Unborn) {
            $this->errorMessage = 'Đứa trẻ này chưa được sinh.';
            return false;
        }

        if ($child->age > 16 && $child->gender == Gender::Male) {
            $this->errorMessage = 'Tuổi của trẻ nam không được lớn hơn 16';
            return false;
        }

        if ($child->age > 15 && $child->gender == Gender::Female) {
            $this->errorMessage = 'Tuổi của trẻ nữ không được lớn hơn 15';
            return false;
        }

        return true;
    }

    public function message(): string
    {
        return $this->errorMessage;
    }
}
