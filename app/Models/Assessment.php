<?php

namespace App\Models;

use App\Enums\Assessment\AssessmentType;
use App\Enums\Child\BornStatus;
use App\Enums\OpenStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Tổng quan đánh giá của Trẻ */
class Assessment extends Model
{
    use HasFactory;

    protected $table = 'assessments';

    protected $fillable = [
        /** ID của bảng con */
        'child_id',
        /** Mô tả */
        'description',
        /** Điểm số */
        'score',
        /** Loại đánh giá */
        'type',
        /** Trạng thái đã kiểm tra hay chưa */
        'checked'

    ];
    protected $casts = [
        'type' => AssessmentType::class,
        'checked' => OpenStatus::class
    ];

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }
}
