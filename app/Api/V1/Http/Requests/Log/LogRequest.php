<?php

namespace App\Api\V1\Http\Requests\Log;

use App\Api\V1\Http\Requests\BaseRequest;

class LogRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    protected function methodGet()
    {
        return [
            'vehicle_entry_id' => 'required|exists:vehicle_entries,id',
        ];
    }
}
