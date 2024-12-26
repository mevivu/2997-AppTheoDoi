<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use App\Enums\Semester\SemesterStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChildEvaluation extends Model
{
    use HasFactory;

    protected $table = 'child_evaluations';

    protected $fillable = [
        /** ID của bảng điểm lớp */
        'class_grade_id',
        /** Điểm trung bình */
        'average_score',
        /** Học lực */
        'academic_performance',
        /** Hạnh kiểm */
        'conduct',
        /**Kỳ học */
        'semester',
        /** Trạng thái  */
        'status'
    ];

    protected $casts = [
        'semester' => SemesterStatus::class,
        'status' => ActiveStatus::class
    ];

    public function subjectGrades(): HasMany
    {
        return $this->hasMany(SubjectGrade::class, 'child_evaluation_id');
    }

    public function qualities(): HasMany
    {
        return $this->hasMany(ChildQuality::class, 'child_evaluation_id');
    }

    public function classGrade(): BelongsTo
    {
        return $this->belongsTo(ClassGrade::class, 'class_grade_id');
    }
}
