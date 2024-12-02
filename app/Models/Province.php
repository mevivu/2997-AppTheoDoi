<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Province extends Model
{
    use HasFactory;

    protected $table = 'provinces';

    protected $guarded = [];

    protected $casts = [];

    public function district(): HasMany
    {

        return $this->hasMany(District::class, 'province_code', 'code');
    }
}
