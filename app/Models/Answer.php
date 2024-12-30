<?php

namespace App\Models;

use App\Enums\Answser\AnswerType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Answer extends Model
{
    use HasFactory;

    protected $table = 'answers';

    protected $fillable = [
        /*Id câu hỏi */
        'question_id',
        /*Nội dung câu trả lời */
        'answer',
        /*Câu trả lời đúng hay sai */
        'is_correct',
        /*Điểm số */
        'score',
        /** Hình ảnh câu trả lời */
        'image',
        /* Loại câu trả trả lời */
        'type'
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'type' => AnswerType::class,
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_id', 'id');
    }

    public function getCorrectAnswer()
    {
        return $this->is_correct ? $this : null;
    }


}
