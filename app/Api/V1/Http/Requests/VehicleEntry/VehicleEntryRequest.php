<?php

namespace App\Api\V1\Http\Requests\VehicleEntry;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\Entry\EntryMode;
use App\Enums\Entry\EntryType;
use Illuminate\Validation\Rule;

class VehicleEntryRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    protected function methodGet()
    {
        return [
            'page' => 'nullable',
            'limit' => 'nullable',
            'keyword' => 'nullable',
            'entry_type' => ['required', Rule::in(EntryType::getValues())],
            'entry_mode' => ['required', Rule::in(EntryMode::getValues())],
        ];
    }

    protected function methodPost(): array
    {
        return [
            'license_plate' => 'required|string|max:15|exists:vehicles,license_plate',
            'customer_id' => 'required|exists:customers,id',
            'entry_type' => ['required', Rule::in(EntryType::getValues())],
            'item_photos' => 'required|array|min:1',
            'item_photos.*' => 'file|image|max:5000',
            'scale_photos' => 'nullable|array|min:1',
            'scale_photos.*' => 'file|image|max:5000',
            'bags' => 'nullable|array',
            'bags.*.values' => 'required',
            'bags.*.notes' => 'nullable|string|max:255',
            'packets' => 'nullable|array',
            'packets.*.values' => 'required',
            'packets.*.notes' => 'nullable|string|max:255',
        ];
    }
}
