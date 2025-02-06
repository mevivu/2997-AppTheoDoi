<?php

namespace App\Api\V1\Http\Resources\Transaction;

use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     * @throws Exception
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'code' => $this->code,
            'created_at' => format_datetime($this->created_at),
            'status' => $this->status,
            'package' => [
                'name' => $this->package->name,
            ]
        ];
    }
}
