<?php

namespace App\Api\V1\Http\Requests\Pregnancy;



use App\Api\V1\Http\Requests\BaseRequest;
use App\Api\V1\Rules\ValidBornChild;

class PregnancyRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodGet(): array
    {
        return [
            'limit' => 'required|integer|min:1',
            'page' => 'required|integer|min:1',
            'child_id' => ['required', 'numeric', 'exists:children,id'],
        ];
    }

    protected function methodPost(): array
    {
        return [
            'child_id' => ['required', 'numeric', 'exists:children,id'],
            'week' => 'nullable|integer|min:1',
            'weight' => 'nullable|numeric|min:0',
            'length' => 'nullable|integer|min:0',
            'head_circumference' => 'nullable|integer|min:0',
            'image' => 'nullable|image|max:5000',
        ];
    }


}
