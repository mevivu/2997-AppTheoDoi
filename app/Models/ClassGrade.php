<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use App\Enums\User\Gender;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassGrade extends Model
{
    use HasFactory;

    protected $table = 'class_grades';

    protected $fillable = [
        /** ID điểm */
        'id',
        /** ID của trẻ */
        'child_id',
        /** ID của lớp */
        'class_id',
        /** Điểm học kỳ 1 */
        'semester1_grade',
        /** Điểm học kỳ 2 */
        'semester2_grade',
        /** Điểm cả năm */
        'full_year_grade',
        /** Trạng thái */
        'status'
    ];

    protected $casts = [
        'status' => ActiveStatus::class,
        'gender' => Gender::class,
    ];



    public function evaluations(): HasMany
    {
        return $this->hasMany(ChildEvaluation::class, 'class_grade_id');
    }

    // public function classes(): BelongsTo
    // {
    //     return $this->belongsTo(Classes::class, 'class_id');
    // }

    // public function children(): BelongsTo
    // {
    //     return $this->belongsTo(Child::class, 'child_id');
    // }
    public function children()
    {
        return $this->belongsTo(Child::class, 'child_id', 'id'); // child_id là khóa ngoại
    }

    public function classes()
    {
        return $this->belongsTo(Classes::class, 'class_id', 'id'); // class_id là khóa ngoại
    }
}
