<?php

namespace App\Enums\User;

use App\Admin\Support\Enum;

/**
 * Cấp Bậc Đối Tác (Affiliate Rank)
 *
 * Xác định thứ hạng và mức độ đóng góp doanh số của người dùng
 * trong hệ thống tiếp thị liên kết (Affiliate) của CHĂM CON 360.
 */
enum AffiliateRank: int
{
    use Enum;

    /** Cấp 1: Bạc (Silver) - Cấp mặc định khi bắt đầu / Còn lại */
    case Silver = 1;

    /** Cấp 2: Vàng (Gold) - Đạt mốc 6.000 user hoặc 10 triệu */
    case Gold = 2;

    /** Cấp 3: Bạch Kim (Platinum) - Đạt mốc 8.000 user hoặc 15 triệu */
    case Platinum = 3;

    /** Cấp 4: Kim Cương (Diamond) - Đạt mốc 10.000 user hoặc 20 triệu */
    case Diamond = 4;

    /**
     * Tên tiếng Việt hiển thị của Cấp bậc
     *
     * @return string
     */
    public function name(): string
    {
        return match ($this) {
            self::Silver => 'Bạc',
            self::Gold => 'Vàng',
            self::Platinum => 'Bạch Kim',
            self::Diamond => 'Kim Cương',
        };
    }

    /**
     * Class CSS màu nền badge giao diện Admin (Tabler UI)
     *
     * @return string
     */
    public function badge(): string
    {
        return match ($this) {
            self::Silver => 'bg-secondary-lt',
            self::Gold => 'bg-yellow-lt',
            self::Platinum => 'bg-indigo-lt',
            self::Diamond => 'bg-cyan-lt',
        };
    }

    /**
     * Tên class icon Tabler Icons
     *
     * @return string
     */
    public function icon(): string
    {
        return match ($this) {
            self::Silver => 'ti ti-medal',
            self::Gold => 'ti ti-crown',
            self::Platinum => 'ti ti-sparkles',
            self::Diamond => 'ti ti-diamond',
        };
    }

    /**
     * Mã màu hex đặc trưng cho cấp bậc
     *
     * @return string
     */
    public function colorHex(): string
    {
        return match ($this) {
            self::Silver => '#8A9BA8',
            self::Gold => '#E6A100',
            self::Platinum => '#6366F1',
            self::Diamond => '#00B4D8',
        };
    }

    /**
     * Lấy cấp bậc tiếp theo cần phấn đấu
     *
     * @return AffiliateRank|null
     */
    public function getNextRank(): ?self
    {
        return match ($this) {
            self::Silver => self::Gold,
            self::Gold => self::Platinum,
            self::Platinum => self::Diamond,
            self::Diamond => null,
        };
    }
}
