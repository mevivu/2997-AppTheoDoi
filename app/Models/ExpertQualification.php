<?php

namespace App\Models;

use App\Enums\DefaultStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpertQualification extends Model
{
    use HasFactory;

    protected $table = 'expert_qualifications';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        /** ID Chuyên gia sở hữu bằng cấp / học vị */
        'expert_id',
        /** Tên bằng cấp / Học vị (VD: Bác sĩ Đa khoa, Thạc sĩ Y học, Bác sĩ Chuyên khoa II, Tiến sĩ...) */
        'degree_name',
        /** Cơ sở đào tạo / Trường đại học cấp bằng (VD: Đại học Y Hà Nội, ĐH Y Dược TP.HCM...) */
        'institution',
        /** Năm tốt nghiệp / Cấp bằng (VD: 2018) */
        'graduation_year',
        /** Chuyên ngành đào tạo chuyên sâu (VD: Nhi khoa, Dinh dưỡng lâm sàng, Thần kinh học...) */
        'specialization',
        /** Thứ tự sắp xếp ưu tiên hiển thị */
        'sort_order',
        /** Trạng thái hiển thị (1: Hiển thị, 2: Bản nháp) */
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => DefaultStatus::class,
        'sort_order' => 'integer',
    ];

    /**
     * Chuyên gia sở hữu bằng cấp này
     */
    public function expert(): BelongsTo
    {
        return $this->belongsTo(Expert::class, 'expert_id');
    }
}
