<?php

namespace App\Api\V1\Http\Requests\Pregnancy;



use App\Api\V1\Http\Requests\BaseRequest;

class PregnancyUpdateRequest extends BaseRequest
{


    protected function methodPost(): array
    {
        return [
            'id' => 'required|integer|exists:pregnancies,id',
            'child_id' => ['required', 'numeric', 'exists:children,id'],
            'week' => 'sometimes|integer|min:1',
            'weight' => 'sometimes|numeric|min:0',
            'length' => 'sometimes|integer|min:0',
            'head_circumference' => 'sometimes|integer|min:0',
            'image' => 'nullable|image|max:5000',
        ];
    }


}
