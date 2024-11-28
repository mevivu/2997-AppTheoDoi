<?php

namespace App\Enums\Setting;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

/**
 * @method static static General()
 * @method static static UserDiscount()
 * @method static static UserUpgrade()
 */
final class SettingGroup extends Enum implements LocalizedEnum
{
    const General = 1;
    const UserDiscount = 2;
    const UserUpgrade = 3;

    const System = 4;
    const C_Ride = 5;
    const C_Car = 6;
    const C_Delivery = 7;
    const C_Intercity = 8;
    const Cost = 9;
    const C_Multi = 10;
}
