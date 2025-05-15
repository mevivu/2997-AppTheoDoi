<?php

namespace App\Models;

use App\Enums\ChildEvaluation\EvaluationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Đánh giá năng lực*/
class ChildQuality extends Model
{
    use HasFactory;

    protected $table = 'child_qualities';

    public $timestamps = false;


    protected $fillable = [
        /** ID của bảng đánh giá */
        'child_evaluation_id',
        /** ID phẩm chất*/
        'quality_id',
        /** Trạng thái */
        'quality_status',
        'remark'
    ];

    protected $casts = [
        'quality_status' => EvaluationStatus::class
    ];

    public function childEvaluation(): BelongsTo
    {
        return $this->belongsTo(ChildEvaluation::class, 'child_evaluation_id');
    }

    public function quality(): BelongsTo
    {
        return $this->belongsTo(Quality::class, 'quality_id');
    }
}
