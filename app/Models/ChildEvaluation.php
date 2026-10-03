<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use App\Enums\ChildEvaluation\AcademicRating;
use App\Enums\ChildEvaluation\ConductRating;
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
        'status',
        /** Học lực tính bởi hệ thống */
        'calculated_academic_performance',
        /** Trạng thái tính toán */
        'calculation_status',
        /** Cờ phụ huynh chủ động ghi đè học lực */
        'is_performance_overridden',
        /** Nhận xét chung của giáo viên */
        'teacher_remark',
        /** Dữ liệu chi tiết tính toán để debug/audit */
        'calculation_snapshot',
        /** Phiên bản công thức tính */
        'calculation_version',
        /** Thời điểm tính toán */
        'calculated_at',
    ];

    protected $casts = [
        'semester' => SemesterStatus::class,
        'status' => ActiveStatus::class,
        'conduct' => ConductRating::class,
        'academic_performance' => AcademicRating::class,
        'calculation_status' => \App\Enums\ReportCard\CalculationStatus::class,
        'is_performance_overridden' => 'boolean',
        'calculation_snapshot' => 'array',
        'calculation_version' => 'integer',
        'calculated_at' => 'datetime',
    ];

    public function attachments(): HasMany
    {
        return $this->hasMany(ChildEvaluationAttachment::class, 'child_evaluation_id')->orderBy('sort_order');
    }

    public function subjectGrades(): HasMany
    {
        return $this->hasMany(SubjectGrade::class, 'child_evaluation_id');
    }

    public function qualities(): HasMany
    {
        return $this->hasMany(ChildQuality::class, 'child_evaluation_id');
    }

    public function capabilities(): HasMany
    {
        return $this->hasMany(ChildCapability::class, 'child_evaluation_id');
    }

    public function classGrade(): BelongsTo
    {
        return $this->belongsTo(ClassGrade::class, 'class_grade_id');
    }
}
