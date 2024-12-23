<?php

namespace App\Api\V1\Http\Resources\Journal;

use App\Api\V1\Support\AuthServiceApi;
use App\Enums\Package\PackageType;
use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use JsonSerializable;

class JournalResource extends JsonResource
{
    use AuthServiceApi;

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     * @throws Exception
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $user = $this->getCurrentUser();
        $package = $user->userPackages->first();
        $isContentVisible = true;
        if ($package->current_type == PackageType::Normal) {
            $isContentVisible = $this->created_at >= Carbon::now()->subYear();
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'type' => $this->type,
            'content' => $this->content,
            'image' => $this->image ? json_decode($this->image) : null,
            'created_at' => format_date($this->created_at),
            'checked' => $isContentVisible
        ];
    }
}
