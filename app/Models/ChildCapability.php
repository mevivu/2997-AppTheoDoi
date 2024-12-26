<?php

namespace App\Models;

use App\Enums\ChildEvaluation\EvaluationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Đánh giá năng lực*/
class ChildCapability extends Model
{
    use HasFactory;

    protected $table = 'child_capabilities';

    public $timestamps = false;


    protected $fillable = [
        /** ID của bảng đánh giá */
        'child_evaluation_id',
        /** ID năng lực*/
        'capability_id',
        /** Trạng thái */
        'capability_status'
    ];

    protected $casts = [
        'capability_status' => EvaluationStatus::class
    ];

    public function childEvaluation(): BelongsTo
    {
        return $this->belongsTo(ChildEvaluation::class, 'child_evaluation_id');
    }

    public function capability(): BelongsTo
    {
        return $this->belongsTo(Capability::class, 'capability_id');
    }
}
