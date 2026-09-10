<?php

namespace App\Enums\User;

use App\Admin\Support\Enum;

/**
 * Cấp Bậc Mẹ Giới Thiệu (Affiliate Rank)
 *
 * Xác định thứ hạng và mức độ đóng góp doanh số của người dùng
 * trong hệ thống tiếp thị liên kết (Affiliate) của CHĂM CON 360.
 */
enum AffiliateRank: int
{
    use Enum;

    /** Cấp 1: Mẹ Đồng (Bronze) - Cấp mặc định khi bắt đầu */
    case Bronze = 1;

    /** Cấp 2: Mẹ Bạc (Silver) - Đạt mốc doanh số sơ cấp */
    case Silver = 2;

    /** Cấp 3: Mẹ Vàng (Gold) - Đạt mốc doanh số trung cấp */
    case Gold = 3;

    /** Cấp 4: Mẹ Kim Cương (Diamond) - Cấp bậc cao quý nhất */
    case Diamond = 4;

    /**
     * Tên tiếng Việt hiển thị của Cấp bậc
     *
     * @return string
     */
    public function name(): string
    {
        return match ($this) {
            self::Bronze => 'Mẹ Đồng',
            self::Silver => 'Mẹ Bạc',
            self::Gold => 'Mẹ Vàng',
            self::Diamond => 'Mẹ Kim Cương',
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
            self::Bronze => 'bg-orange-lt',
            self::Silver => 'bg-secondary-lt',
            self::Gold => 'bg-yellow-lt',
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
            self::Bronze => 'ti ti-medal',
            self::Silver => 'ti ti-award',
            self::Gold => 'ti ti-crown',
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
            self::Bronze => '#CD7F32',
            self::Silver => '#8A9BA8',
            self::Gold => '#E6A100',
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
            self::Bronze => self::Silver,
            self::Silver => self::Gold,
            self::Gold => self::Diamond,
            self::Diamond => null,
        };
    }
}
