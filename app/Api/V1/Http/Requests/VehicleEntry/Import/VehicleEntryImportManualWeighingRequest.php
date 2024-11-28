<?php

namespace App\Api\V1\Http\Requests\VehicleEntry\Import;

use App\Api\V1\Http\Requests\BaseRequest;



class VehicleEntryImportManualWeighingRequest extends BaseRequest
{

    protected function methodPost(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\EntryStage,id'],
            'cancellation_reason' => 'nullable',
            'item_type_id' => ['required', 'exists:App\Models\ItemType,id'],
            'item_photo' => 'required|array|min:1',
            'item_photo.*' => 'file|image|max:5000',
            'scale_photo' => 'nullable|array|min:1',
            'scale_photo.*' => 'file|image|max:5000',
            'packets' => ['nullable', 'array', ],
            'packets.*.number_of_bags' => ['required', 'integer', 'min:1'],
            'packets.*.weight' => ['required', 'numeric', 'min:0.01'],
            'packets.*.tare_weight' => ['nullable', 'numeric', 'min:0'],
            'packets.*.notes' => ['nullable', 'string', 'max:255'],
        ];
    }
}
