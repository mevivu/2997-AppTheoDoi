<?php

namespace App\Models;

use App\Enums\Lesson\LessonDifficultyRating;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonDifficultyRatingModel extends Model
{
    use HasFactory;

    protected $table = 'lesson_difficulty_ratings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        /** ID phụ huynh đánh giá */
        'user_id',
        /** ID bé được đánh giá */
        'child_id',
        /** ID bài học */
        'lesson_id',
        /** Mức đánh giá: easy, with_help, hard */
        'difficulty_level',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'user_id' => 'integer',
        'child_id' => 'integer',
        'lesson_id' => 'integer',
        'difficulty_level' => LessonDifficultyRating::class,
    ];

    /**
     * Phụ huynh đã đánh giá
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Bé được đánh giá
     */
    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class, 'child_id');
    }

    /**
     * Bài học được đánh giá
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
