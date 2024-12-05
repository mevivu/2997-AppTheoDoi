<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id', 'id');
    }

    public function getCorrectAnswer()
    {
        return $this->is_correct ? $this : null;
    }


}
