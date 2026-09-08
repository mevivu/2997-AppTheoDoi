<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeatureUsage extends Model
{
    use HasFactory;

    protected $table = 'feature_usages';

    protected $fillable = [
        'user_id',
        'child_id',
        'feature_code',
        'feature_name',
        'category',
        'action',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->feature_name) || empty($model->category)) {
                $catalog = \App\Admin\Services\FeatureStatisticsService::getFeatureCatalog();
                $meta = $catalog[$model->feature_code] ?? null;
                if (empty($model->feature_name)) {
                    $model->feature_name = $meta['name'] ?? ucfirst($model->feature_code);
                }
                if (empty($model->category)) {
                    $model->category = $meta['category'] ?? 'evaluation';
                }
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class, 'child_id');
    }
}
