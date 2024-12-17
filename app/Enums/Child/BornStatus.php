<?php

namespace App\Enums\Child;


use App\Admin\Support\Enum;

enum BornStatus: string
{
    use Enum;

    // Đã sinh
    case Born = 'born';
    // Chưa sinh
    case Unborn = 'unborn';


    public function badge(): string
    {
        return match ($this) {
            BornStatus::Born => 'bg-green',
            BornStatus::Unborn => 'bg-yellow',
        };
    }
}
