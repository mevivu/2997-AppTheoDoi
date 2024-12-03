<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Province extends Model
{
    use HasFactory;

    protected $table = 'provinces';


    protected $casts = [];

    public function districts(): HasMany
    {
        return $this->hasMany(District::class, 'province_code', 'code');
    }

    public function wards(): HasManyThrough
    {
        return $this->hasManyThrough(Ward::class, District::class, 'province_code', 'district_code', 'code', 'code');
    }
}
