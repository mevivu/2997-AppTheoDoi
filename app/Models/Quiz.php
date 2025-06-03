<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use App\Enums\Question\AgeGroup;
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
        'status',
        /** Loại */
        'age_group'

    ];
    protected $casts = [
        'type' => QuestionType::class,
        'status' => ActiveStatus::class,
        'age_group' => AgeGroup::class,

    ];

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'quiz_questions')
            ->withPivot('sequence')
            ->orderBy('quiz_questions.sequence');
    }
}
