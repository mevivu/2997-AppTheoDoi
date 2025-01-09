<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use App\Enums\Guide\GuideType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Hướng dẫn */
class Guide extends Model
{
    use HasFactory;


    protected $table = 'guides';


    protected $fillable = [
        /* Tiêu đề của hướng dẫn */
        'title',
        /* Mô tả của hướng dẫn */
        'description',
        /* Loại hướng dẫn (Sức mạnh hoặc Sức bền) */
        'type',
        /* Trạng thái của hướng dẫn (Dự thảo hoặc Kích hoạt) */
        'status',
    ];

    protected $casts = [
        'type' => GuideType::class,
        'status' => ActiveStatus::class,
    ];

    public function steps(): HasMany
    {
        return $this->hasMany(Step::class, 'guide_id');
    }
}
