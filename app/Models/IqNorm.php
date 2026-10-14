<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Bảng chuẩn độ lệch Wechsler (Wechsler Deviation IQ Norms) theo từng độ tuổi
 *
 * @property int $id
 * @property int $age
 * @property float $mean_score
 * @property float $sd_score
 * @property int $sample_size
 * @property float|null $min_raw_score
 * @property float|null $max_raw_score
 * @property string $version
 * @property ActiveStatus $status
 * @property string|null $note
 */
class IqNorm extends Model
{
    use HasFactory;

    protected $table = 'iq_norms';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        /** Độ tuổi của trẻ (1 - 16 tuổi) */
        'age',
        /** Điểm thô trung bình của nhóm tuổi (Mean μ) */
        'mean_score',
        /** Độ lệch chuẩn của nhóm tuổi (StdDev σ) */
        'sd_score',
        /** Số lượng trẻ trong tập mẫu khảo sát chuẩn hóa (N) */
        'sample_size',
        /** Điểm thô thấp nhất ghi nhận trong tập mẫu */
        'min_raw_score',
        /** Điểm thô cao nhất ghi nhận trong tập mẫu */
        'max_raw_score',
        /** Phiên bản bảng chuẩn (vd: baseline_v1) */
        'version',
        /** Trạng thái kích hoạt (active, draft, deleted) */
        'status',
        /** Ghi chú của chuyên gia hoặc mô tả mẫu dữ liệu */
        'note',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'age' => 'integer',
        'mean_score' => 'float',
        'sd_score' => 'float',
        'sample_size' => 'integer',
        'min_raw_score' => 'float',
        'max_raw_score' => 'float',
        'status' => ActiveStatus::class,
    ];

    /**
     * Tự động xóa cache khi có thay đổi bản ghi
     */
    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('iq_norms_all_active');
        });
        static::deleted(function () {
            Cache::forget('iq_norms_all_active');
        });
    }

    /**
     * Tra cứu bảng chuẩn cho độ tuổi chỉ định (kèm cơ chế Fallback an toàn)
     */
    public static function getNormForAge(int $age): ?self
    {
        $allNorms = Cache::remember('iq_norms_all_active', 3600, function () {
            return static::where('status', ActiveStatus::Active->value)
                ->orderBy('age', 'asc')
                ->get()
                ->keyBy('age');
        });

        if ($allNorms->isEmpty()) {
            return null;
        }

        // 1. Tìm đúng tuổi
        if (isset($allNorms[$age])) {
            return $allNorms[$age];
        }

        // 2. Nếu tuổi nhỏ hơn min -> lấy min
        $minAge = $allNorms->keys()->min();
        if ($age < $minAge) {
            return $allNorms[$minAge];
        }

        // 3. Nếu tuổi lớn hơn max -> lấy max
        $maxAge = $allNorms->keys()->max();
        if ($age > $maxAge) {
            return $allNorms[$maxAge];
        }

        // 4. Tìm tuổi gần nhất
        $closestAge = null;
        $minDiff = PHP_INT_MAX;
        foreach ($allNorms->keys() as $normAge) {
            $diff = abs($normAge - $age);
            if ($diff < $minDiff) {
                $minDiff = $diff;
                $closestAge = $normAge;
            }
        }

        return $closestAge ? $allNorms[$closestAge] : null;
    }

    /**
     * Tính chỉ số IQ độ lệch Wechsler từ điểm thô
     * Công thức: IQ = Round(100 + 15 * (rawScore - mean) / sd)
     */
    public function calculateIq(float $rawScore): int
    {
        if ($this->sd_score <= 0) {
            return 100;
        }

        $zScore = ($rawScore - $this->mean_score) / $this->sd_score;
        $iq = (int) round(100 + (15 * $zScore));

        // Giới hạn trần/sàn theo chuẩn lâm sàng (Floor 40, Ceiling 160)
        return max(40, min(160, $iq));
    }

    /**
     * Phân loại trí tuệ theo chuẩn Wechsler quốc tế
     */
    public static function getClassification(int $iq): string
    {
        return match (true) {
            $iq >= 130 => 'Rất ưu tú (Xuất sắc)',
            $iq >= 120 => 'Ưu tú (Thông minh cao)',
            $iq >= 110 => 'Trên trung bình',
            $iq >= 90 => 'Trung bình',
            $iq >= 80 => 'Dưới trung bình',
            $iq >= 70 => 'Ranh giới',
            default => 'Rất thấp',
        };
    }

    /**
     * CSS class badge cho phân loại trí tuệ
     */
    public static function getClassificationBadgeClass(int $iq): string
    {
        return match (true) {
            $iq >= 130 => 'bg-purple-lt text-purple fw-bold',
            $iq >= 120 => 'bg-blue-lt text-blue fw-bold',
            $iq >= 110 => 'bg-cyan-lt text-cyan fw-bold',
            $iq >= 90 => 'bg-green-lt text-green fw-bold',
            $iq >= 80 => 'bg-yellow-lt text-warning fw-bold',
            $iq >= 70 => 'bg-orange-lt text-orange fw-bold',
            default => 'bg-red-lt text-danger fw-bold',
        };
    }
}
