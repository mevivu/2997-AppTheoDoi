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
        $package = $user->userPackages->first();
        $isContentVisible = true;
        if ($package->current_type == PackageType::Normal) {
//            $day = Carbon::now()->subDay();
            $year =Carbon::now()->subYear();
            $isContentVisible = $time >= $year;
        }
        return $isContentVisible;

    }

}
