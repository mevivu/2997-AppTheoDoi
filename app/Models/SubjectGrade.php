<?php

namespace App\Models;

use App\Enums\SubjectGrade\AchievementLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubjectGrade extends Model
{
    use HasFactory;

    protected $table = 'subject_grades';

    public $timestamps = false;

    protected $fillable = [
        /** ID của bảng đánh giá */
        'child_evaluation_id',
        /** ID môn học */
        'subject_id',
        /** Điểm số*/
        'grade',
        /** Nhận xét */
        'remark',
        /** Mức đạt được */
        'achievement_level'

    ];

    protected $casts = [
        'achievement_level' => AchievementLevel::class
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function childEvaluation(): BelongsTo
    {
        return $this->belongsTo(ChildEvaluation::class, 'child_evaluation_id');
    }
}
