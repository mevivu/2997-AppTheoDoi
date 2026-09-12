<?php

namespace App\Api\V1\Http\Resources\Auth;

use App\AES\AESHelper;
use App\Api\V1\Http\Resources\Package\AuthPackageResource;
use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class AuthResource extends JsonResource
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
            'code' => $this->code,
            'affiliate_code' => $this->affiliate_code,
            'username' => $this->username ? AESHelper::decrypt($this->username) : null,
            'fullname' => $this->fullname,
            'slug' => $this->slug,
            'email' => $this->email ? AESHelper::decrypt($this->email) : null,
            'phone' => $this->phone ? AESHelper::decrypt($this->phone) : null,
            'address' => $this->address ? AESHelper::decrypt($this->address) : null,
            'gender' => $this->gender,
            'active' => $this->active,
            'lng' => $this->longitude,
            'lat' => $this->latitude,
            'birthday' => $this->birthday,
            'avatar' => formatImageUrl($this->avatar),
            'notification_preference' => $this->notification_preference,
            'father_name' => $this->father_name,
            'father_height' => $this->father_height,
            'father_birthday' => $this->father_birthday,
            'mother_name' => $this->mother_name,
            'mother_height' => $this->mother_height,
            'mother_birthday' => $this->mother_birthday,
            'status' => $this->status,
            'bank_id' => $this->bank_id,
            'bank_code' => $this->bank_code,
            'bank_name' => $this->bank_name,
            'bank_account_number' => $this->bank_account_number,
            'bank_account_name' => $this->bank_account_name,
            'bank_logo' => $this->bank?->logo,
            'wallet_balance' => (float) ($this->wallet_balance ?? 0),
            'affiliate_rank' => $this->affiliate_rank?->value ?? 1,
            'affiliate_rank_name' => $this->affiliate_rank?->name() ?? 'Bạc',
            'affiliate_total_sales' => (float) ($this->affiliate_total_sales ?? 0),
            'affiliate_terms_accepted' => !empty($this->affiliate_terms_accepted_at),
            'affiliate_terms_accepted_at' => $this->affiliate_terms_accepted_at?->toISOString(),
            'referrals_count' => $this->referrals()->count(),
            'referrer' => $this->referrer ? [
                'id' => $this->referrer->id,
                'fullname' => $this->referrer->fullname,
                'affiliate_code' => $this->referrer->affiliate_code,
            ] : null,
            'created_at' => format_date($this->created_at),
            'package' => new AuthPackageResource($this->userPackages->first())
        ];
    }
}
