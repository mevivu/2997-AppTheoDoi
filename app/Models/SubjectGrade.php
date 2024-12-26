<?php

namespace App\Models;

use App\Enums\Semester\SemesterStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubjectGrade extends Model
{
    use HasFactory;

    protected $table = 'class_grades';

    protected $fillable = [
        /** ID của bảng điểm lớp */
        'class_grade_id',
        /** ID môn học */
        'subject_id',
        /** Điểm số*/
        'grade',
        /**Kỳ học */
        'semester',

    ];

    protected $casts = [
        'semester' => SemesterStatus::class
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function classGrade(): BelongsTo
    {
        return $this->belongsTo(ClassGrade::class, 'class_grade_id');
    }
}
