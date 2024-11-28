<?php

namespace App\Admin\Repositories\Otp;
use App\Admin\Repositories\EloquentRepository;
use App\Models\Otp;

class OtpRepository extends EloquentRepository implements OtpRepositoryInterface
{

    protected $select = [];

    public function getModel(): string
    {
        return Otp::class;
    }

}
