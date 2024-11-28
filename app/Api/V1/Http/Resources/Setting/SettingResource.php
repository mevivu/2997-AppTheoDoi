<?php
namespace App\Api\V1\Http\Resources\Setting;

use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'setting_key' => $this->setting_key,
            'setting_name' => $this->setting_name,
            'plain_value' => $this->plain_value,
            'desc' => $this->desc,
            'type_input' => $this->type_input,
            'type_data' => $this->type_data,
            'group' => $this->group,
            'created_at' => format_datetime($this->created_at),
            'updated_at' => format_datetime($this->updated_at),
        ];
    }
}