<?php

namespace App\Api\V1\Support;


use App\Enums\Package\PackageType;
use Illuminate\Support\Carbon;

trait CheckPackage
{
    use AuthServiceApi;

    public function checkUserPackage($time): bool
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return true;
        }

        $package = $user->userPackages?->first();
        $currentType = $package?->current_type ?? PackageType::Normal;
        $isContentVisible = true;

        if ($currentType == PackageType::Normal && $time) {
            $year = Carbon::now()->subYear();
            $isContentVisible = Carbon::parse($time)->gte($year);
        }

        return $isContentVisible;
    }

}
