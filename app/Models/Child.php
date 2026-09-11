<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use App\Enums\Assessment\AssessmentType;
use App\Enums\Child\BornStatus;
use App\Enums\OpenStatus;
use App\Enums\Permission\PermissionType;
use App\Enums\Question\QuestionType;
use App\Enums\User\Gender;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\Child\ChildStatus;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Child extends Model
{
    use HasFactory;

    protected $table = 'children';

    protected $fillable = [
        /** Họ tên */
        'fullname',
        /** Tuổi */
        'age',
        /** Tháng */
        'month',
        /** Ngày sinh */
        'birthday',
        /** Ngày dự sinh */
        'due_date',
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
        'due_date' => 'date',
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

    public function classGrades(): HasMany
    {
        return $this->hasMany(ClassGrade::class, 'child_id');
    }

    public function vaccinationSchedules(): HasMany
    {
        return $this->hasMany(VaccinationSchedule::class);
    }

    public function ratingPQs(): HasMany
    {
        return $this->hasMany(RatingPQ::class, 'child_id');
    }

    protected static function boot(): void
    {
        parent::boot();

        static::created(function ($child) {
            $descriptions = self::getAssessmentDescriptions();
            $class = SchoolClass::where('status', ActiveStatus::Active)->get();

            // create assessment
            foreach (AssessmentType::cases() as $type) {
                Assessment::create([
                    'child_id' => $child->id,
                    'type' => $type->value,
                    'description' => $descriptions[$type->value] ?? null,
                    'score' => null,
                    'checked' => OpenStatus::OFF->value,
                ]);
            }
            // create class grades
            foreach ($class as $item) {
                ClassGrade::create([
                    'child_id' => $child->id,
                    'class_id' => $item->id,
                    'semester1_grade' => null,
                    'semester2_grade' => null,
                    'full_year_grade' => null,
                    'status' => ActiveStatus::Draft->value,
                ]);
            }

            //create vaccination schedule admin
            $vaccinationSchedules = VaccinationSchedule::where('type', PermissionType::ADMIN)
                ->where('status', ActiveStatus::Active)
                ->get();
            foreach ($vaccinationSchedules as $schedule) {
                VaccinationSchedule::create([
                    'child_id' => $child->id,
                    'name' => $schedule->name,
                    'description' => $schedule->description,
                    'image' => $schedule->image,
                    'performed_on' => $schedule->performed_on,
                    'vaccination_status' => $schedule->vaccination_status,
                    'vaccination_type_id' => $schedule->vaccination_type_id,
                    'type' => PermissionType::USER
                ]);
            }
            for ($age = 1; $age <= 16; $age++) {
                Rating::create(
                    [
                        'child_id' => $child->id,
                        'age' => $age,
                        'type' => QuestionType::IQ,
                    ]
                );
            }

            // Chống gian lận Affiliate: Khi user mới tạo hồ sơ con lần đầu,
            // trigger cộng hoa hồng cho người giới thiệu (deferred reward)
            try {
                $user = $child->user;
                if ($user && $user->pending_referral_reward && !empty($user->referrer_id)) {
                    // Chỉ trigger khi đây là hồ sơ con đầu tiên
                    $childCount = Child::where('user_id', $user->id)->count();
                    if ($childCount === 1) {
                        app(\App\Services\Affiliate\AffiliateServiceInterface::class)
                            ->processCompletedChildProfile($user);
                    }
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error(
                    'Lỗi khi trigger hoa hồng affiliate sau tạo hồ sơ con: ' . $e->getMessage(),
                    ['child_id' => $child->id, 'user_id' => $child->user_id]
                );
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
