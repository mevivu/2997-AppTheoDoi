<?php

namespace App\Admin\Http\Requests\WeightHeight;

use App\Admin\Http\Requests\BaseRequest;
use App\Admin\Rules\EmailUnique;
use App\Admin\Rules\PhoneUnique;
use App\Enums\ActiveStatus;
use App\Enums\User\Gender;
use App\Enums\User\UserStatus;
use Illuminate\Validation\Rules\Enum;

class WeightHeightRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost(): array
    {
        return [
            'weight'=>['required','numeric'],
            'height'=>['required','numeric'],
            'age'=>['required','integer'],
            'month'=>['required','numeric','between:1,12'],
            'gender'=>['required',new Enum(Gender::class)],
            'status'=>['required',new Enum(ActiveStatus::class)],
        ];
    }
    protected function methodPut(): array
    {
        return [
            'id'=>['required','exists:App\Models\WeightHeightWHO,id'],
            'weight'=>['required','numeric'],
            'height'=>['required','numeric'],
            'age'=>['required','integer'],
            'month'=>['required','numeric','between:1,12'],
            'gender'=>['required',new Enum(Gender::class)],
            'status'=>['required',new Enum(ActiveStatus::class)],
        ];
    }


}
