<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubjectGrade extends Model
{
    use HasFactory;

    protected $table = 'class_grades';

    public $timestamps = false;

    protected $fillable = [
        /** ID của bảng điểm lớp */
        'class_grade_id',
        /** ID môn học */
        'subject_id',
        /** Điểm số*/
        'grade',

    ];

    protected $casts = [

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
