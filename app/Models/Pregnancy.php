<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Thai kì */
class Pregnancy extends Model
{
    use HasFactory;

    protected $table = 'pregnancies';

    protected $fillable = [
        /** ID của đứa trẻ */
        'child_id',
        /** Ngày bắt đầu thai kì */
        'start_date',
        /** Ngày dự sinh */
        'end_date',
        /** Tuần thai hiện tại */
        'week',
        /** Cân nặng của em bé (kg) */
        'weight',
        /** Chiều dài của em bé (cm) */
        'length',
        /** Chu vi đầu của em bé (cm) */
        'head_circumference',
        /** Trạng thái của thai kì */
        'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'status' => ActiveStatus::class,
    ];

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class, 'child_id');
    }


}
