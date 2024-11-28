<?php

namespace App\Api\V1\Http\Requests\EntryStage;

use App\Api\V1\Http\Requests\BaseRequest;

class EntryStageRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    protected function methodGet()
    {
        return [
            'entry_id' => 'required|exists:vehicle_entries,id',
        ];
    }
}