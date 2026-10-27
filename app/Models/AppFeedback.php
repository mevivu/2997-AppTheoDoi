<?php

namespace App\Models;

use App\Enums\Feedback\AppFeedbackStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppFeedback extends Model
{
    use HasFactory;

    public const SETTING_REWARD_PACKAGE_ID = 'feedback_reward_package_id';

    protected $table = 'app_feedbacks';

    protected $fillable = [
        'user_id',
        'email',
        'rating',
        'favorite_feature',
        'improvement_feedback',
        'status',
        'granted_package_id',
        'vip_granted_at',
        'granted_by_admin_id',
    ];

    protected $casts = [
        'rating' => 'integer',
        'status' => AppFeedbackStatus::class,
        'vip_granted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function grantedPackage(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'granted_package_id');
    }

    public function grantedByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'granted_by_admin_id');
    }
}
