<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use App\Enums\Class\EducationLevel;
use App\Enums\Class\LevelGroup;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SchoolClass extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'name',
        'status',
        'level_group',
        'education_level',
    ];

    protected $casts = [
        'status' => ActiveStatus::class,
        'level_group' => LevelGroup::class,
        'education_level' => EducationLevel::class,
    ];

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'class_subject', 'class_id', 'subject_id')
            ->withPivot('evaluation_method', 'is_required', 'sort_order');
    }

    public function resolvedEducationLevel(): EducationLevel
    {
        return $this->education_level ?? EducationLevel::fromClassId($this->id);
    }
}
