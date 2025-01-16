<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Đánh giá PQ*/
class RatingPQ extends Model
{
    use HasFactory;

    protected $table = 'ratings_pqs';

    protected $fillable = [
        /** Ngày đánh giá */
        'assessment_date',
        /** Chiều cao, đơn vị cm */
        'height',
        /** Cân nặng, đơn vị kg */
        'weight',
        /** Điểm sức mạnh */
        'strength',
        /** Điểm sức bền */
        'endurance',
        /** Chỉ số BMI */
        'bmi',
        /** Kết quả chỉ số BMI */
        'bmi_result',
        /** Child ID */
        'child_id',
    ];
    protected $casts = [

    ];


    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class, 'child_id');
    }


}
