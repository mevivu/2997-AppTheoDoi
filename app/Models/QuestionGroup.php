<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuestionGroup extends Model
{
    use HasFactory;

    protected $table = 'question_groups';

    protected $fillable = [
        /* Tên */
        'name',
        /* Mô tả */
        'description',
        /* Trạng thái */
        'status',
    ];

    protected $casts = [
        'status' => ActiveStatus::class,
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'question_group_id', 'id');
    }
}
