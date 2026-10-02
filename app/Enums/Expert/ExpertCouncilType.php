<?php

namespace App\Enums\Expert;

use App\Admin\Support\Enum;

enum ExpertCouncilType: int
{
    use Enum;

    case Strategic = 1;
    case Professional = 2;

    public function badge(): string
    {
        return match ($this) {
            ExpertCouncilType::Strategic => 'bg-indigo-lt',
            ExpertCouncilType::Professional => 'bg-teal-lt',
        };
    }

    public function label(): string
    {
        return match ($this) {
            ExpertCouncilType::Strategic => 'Hội đồng Cố vấn Chiến lược',
            ExpertCouncilType::Professional => 'Hội đồng Tư vấn Chuyên môn',
        };
    }

    public static function asSelectArray(): array
    {
        $array = [];
        foreach (self::cases() as $item) {
            $array[$item->value] = $item->label();
        }
        return $array;
    }
}
