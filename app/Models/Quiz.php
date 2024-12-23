<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/** Bài kiểm tra */
class Quiz extends Model
{
    use HasFactory;

    protected $table = 'quizzes';

    protected $fillable = [
        /* Tên  */
        'title',
        /* Mô tả */
        'description',
        /* Trạng thái */
        'status'

    ];
    protected $casts = [

        'status' => ActiveStatus::class,
    ];

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'quiz_questions');
    }




}
