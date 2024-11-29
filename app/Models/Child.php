<?php

namespace App\Models;

use App\Enums\User\Gender;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Child extends Model
{
    use HasFactory;

    protected $table = 'children';

    protected $fillable = [
        'fullname',
        'birthday',
        'gender',
        'user_id',
    ];
    protected $casts = [
        'birthday' => 'date',
        'gender' => Gender::class,
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
