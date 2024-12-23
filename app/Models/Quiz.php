<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use App\Enums\Question\QuestionType;
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
        /** Tuổi */
        'age',
        /** Loại */
        'type',
        /* Trạng thái */
        'status'

    ];
    protected $casts = [
        'type' => QuestionType::class,
        'status' => ActiveStatus::class,
    ];

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'quiz_questions');
    }




}
