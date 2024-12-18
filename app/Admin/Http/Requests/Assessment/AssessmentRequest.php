<?php

namespace App\Admin\Http\Requests\Assessment;

use App\Admin\Http\Requests\BaseRequest;



class AssessmentRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodGet(): array
    {
        return [
            'child_id' => ['required','numeric', 'exists:children,id'],
        ];
    }


}
