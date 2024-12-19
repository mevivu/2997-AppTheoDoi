<?php

namespace App\Api\V1\Http\Requests\Pregnancy;

use App\Admin\Http\Requests\BaseRequest;


class PregnancyUpdateRequest extends BaseRequest
{


    protected function methodPost(): array
    {
        return [
            'id' => 'required|integer|exists:pregnancies,id',
            'child_id' => 'required|integer|exists:children,id',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d',
            'week' => 'sometimes|integer|min:1',
            'weight' => 'sometimes|numeric|min:0',
            'length' => 'sometimes|integer|min:0',
            'head_circumference' => 'sometimes|integer|min:0',
            'image' => 'required|image|max:5000',
        ];
    }


}
