<?php

namespace App\Models;

use App\Enums\User\Gender;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\Child\ChildStatus;

class Child extends Model
{
    use HasFactory;

    protected $table = 'children';

    protected $fillable = [
        'fullname',
        'birthday',
        'gender',
        'user_id',
        'status'
    ];
    protected $casts = [
        'birthday' => 'date',
        'gender' => Gender::class,
        'status' => ChildStatus::class,
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
