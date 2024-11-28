<?php

namespace App\Enums;


use App\Admin\Support\Enum;

enum DeleteStatus: string
{
    use Enum;

    case Deleted = 'deleted';

    case NotDeleted = 'not_deleted';


    public function badge(): string
    {
        return match ($this) {
            DeleteStatus::Deleted => 'bg-red',
            DeleteStatus::NotDeleted => 'bg-blue',

        };
    }
}