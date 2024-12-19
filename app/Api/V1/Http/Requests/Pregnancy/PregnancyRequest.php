<?php

namespace App\Api\V1\Http\Requests\Pregnancy;

use App\Admin\Http\Requests\BaseRequest;


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
