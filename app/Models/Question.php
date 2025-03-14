<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use App\Enums\Question\AgeGroup;
use App\Enums\Question\QuestionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $table = 'questions';

    protected $fillable = [
        /* Nhóm câu hỏi - Áp dụng cho EQ, AQ */
        'question_group_id',
        /* Tuổi - Áp dụng cho IQ */
        'age',
        /* Tuổi áp dụng - Áp dụng cho EQ, AQ */
        'age_group',
        /* Câu hỏi */
        'question',
        /* Hình ảnh câu hỏi */
        'question_image',
        /* Loại câu hỏi */
        'question_type',
        /* Trạng thái */
        'status',
    ];

    protected $casts = [
        'status' => ActiveStatus::class,
        'question_type' => QuestionType::class,
        'age_group' => AgeGroup::class,
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(QuestionGroup::class, 'question_group_id', 'id');
    }
    public function quizzes(): BelongsToMany
    {
        return $this->belongsToMany(Quiz::class, 'quiz_questions');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class, 'question_id', 'id');
    }
}
