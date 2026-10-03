<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\URL;

class ChildEvaluationAttachment extends Model
{
    use HasFactory;

    protected $table = 'child_evaluation_attachments';

    protected $fillable = [
        'child_evaluation_id',
        'disk',
        'file_path',
        'original_name',
        'mime_type',
        'size_bytes',
        'width',
        'height',
        'sort_order',
        'uploaded_by',
    ];

    protected $casts = [
        'size_bytes' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'sort_order' => 'integer',
    ];

    protected $appends = [
        'url',
    ];

    public function childEvaluation(): BelongsTo
    {
        return $this->belongsTo(ChildEvaluation::class, 'child_evaluation_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getUrlAttribute(): ?string
    {
        if (!$this->exists) {
            return null;
        }

        try {
            return URL::temporarySignedRoute(
                'api.v1.child-evaluations.attachments.file',
                now()->addMinutes(30),
                ['attachmentId' => $this->id]
            );
        } catch (\Throwable $e) {
            return null;
        }
    }
}
