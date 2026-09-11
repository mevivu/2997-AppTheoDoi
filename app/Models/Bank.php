<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;

    protected $table = 'banks';

    public $timestamps = false;

    protected $fillable = [
        'bin',
        'shortName',
        'logo',
        'transfer_supported',
        'lookup_supported',
        'support',
        'is_transfer',
        'swift_code',
        'name',
        'code',
    ];

    protected $casts = [
        'transfer_supported' => 'integer',
        'lookup_supported' => 'integer',
        'support' => 'integer',
        'is_transfer' => 'integer',
    ];
}
