<?php

namespace App\Models;

use App\Enums\Assessment\AssessmentType;
use App\Enums\Child\BornStatus;
use App\Enums\OpenStatus;
use App\Enums\User\Gender;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\Child\ChildStatus;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Child extends Model
{
    use HasFactory;

    protected $table = 'children';

    protected $fillable = [
        /** Họ tên */
        'fullname',
        /** Ngày sinh */
        'birthday',
        /** Giới tính */
        'gender',
        /** Hình ảnh */
        'avatar',
        /** User ID */
        'user_id',
        /** Trạng thái */
        'status',
        /** Trạng thái sinh */
        'is_born'
    ];
    protected $casts = [
        'birthday' => 'date',
        'gender' => Gender::class,
        'status' => ChildStatus::class,
        'is_born' => BornStatus::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class, 'child_id');
    }

    protected static function boot(): void
    {
        parent::boot();

        static::created(function ($child) {
            $descriptions = self::getAssessmentDescriptions();
            foreach (AssessmentType::cases() as $type) {
                Assessment::create([
                    'child_id' => $child->id,
                    'type' => $type->value,
                    'description' => $descriptions[$type->value] ?? null,
                    'score' => null,
                    'checked' => OpenStatus::OFF->value,
                ]);
            }
        });
    }

    public static function getAssessmentDescriptions(): array
    {
        return [
            AssessmentType::PQ->value => 'Thực hiện đánh giá thể chất (PQ)',
            AssessmentType::IQ->value => 'Thực hiện đánh giá trí tuệ (IQ)',
            AssessmentType::EQ->value => 'Thực hiện đánh giá cảm xúc (EQ)',
            AssessmentType::GPA->value => 'Thực hiện đánh giá học lực (GPA)',
            AssessmentType::AQ->value => 'Thực hiện đánh giá khả năng vượt khó (AQ)'
        ];
    }
}
