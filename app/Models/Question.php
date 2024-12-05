<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use App\Enums\Question\QuestionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $table = 'questions';

    protected $fillable = [
        /* Nhóm câu hỏi - Áp dụng cho EQ, AQ */
        'question_group_id',
        /* Tuổi - Áp dụng cho IQ */
        'age',
        /* Câu hỏi */
        'question',
        /* Loại câu hỏi */
        'question_type',
        /* Trạng thái */
        'status',
    ];

    protected $casts = [
        'status' => ActiveStatus::class,
        'question_type' => QuestionType::class,
    ];

    public function group()
    {
        return $this->belongsTo(QuestionGroup::class, 'question_group_id', 'id');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class, 'question_id', 'id');
    }
}
