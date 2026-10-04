<?php

namespace App\Enums\User;

use App\Admin\Support\Enum;

/**
 * Cấp Bậc Phụ Huynh (Parent Rank)
 *
 * Phân hạng thành viên phụ huynh dựa trên:
 * 1. Thời gian sử dụng app trong tháng
 * 2. Tần suất sử dụng app trong tháng
 * 3. Số bài đánh giá đã thực hiện cho con
 * 4. Điểm tổng hợp các chỉ số của con
 */
enum ParentRank: int
{
    use Enum;

    /** Cấp 0: Thành viên mới - Dưới 20 điểm */
    case NewMember = 0;

    /** Cấp 1: Đồng - Từ 20 điểm */
    case Bronze = 1;

    /** Cấp 2: Bạc - Từ 40 điểm */
    case Silver = 2;

    /** Cấp 3: Vàng - Từ 60 điểm */
    case Gold = 3;

    /** Cấp 4: Kim Cương - Từ 80 điểm */
    case Diamond = 4;

    /**
     * Tên tiếng Việt hiển thị của Cấp bậc
     */
    public function name(): string
    {
        return match ($this) {
            self::NewMember => 'Thành viên mới',
            self::Bronze => 'Đồng',
            self::Silver => 'Bạc',
            self::Gold => 'Vàng',
            self::Diamond => 'Kim Cương',
        };
    }

    /**
     * Tên tiếng Việt hiển thị của Cấp bậc (Alias của name())
     */
    public function label(): string
    {
        return $this->name();
    }

    /**
     * Class CSS màu nền badge giao diện Admin (Tabler UI)
     */
    public function badge(): string
    {
        return match ($this) {
            self::NewMember => 'bg-muted-lt',
            self::Bronze => 'bg-orange-lt',
            self::Silver => 'bg-secondary-lt',
            self::Gold => 'bg-yellow-lt',
            self::Diamond => 'bg-cyan-lt',
        };
    }

    /**
     * Tên class icon Tabler Icons
     */
    public function icon(): string
    {
        return match ($this) {
            self::NewMember => 'ti ti-user',
            self::Bronze => 'ti ti-shield',
            self::Silver => 'ti ti-medal',
            self::Gold => 'ti ti-crown',
            self::Diamond => 'ti ti-diamond',
        };
    }

    /**
     * Mã màu hex đặc trưng cho cấp bậc
     */
    public function colorHex(): string
    {
        return match ($this) {
            self::NewMember => '#94A3B8',
            self::Bronze => '#CD7F32',
            self::Silver => '#8A9BA8',
            self::Gold => '#E6A100',
            self::Diamond => '#00B4D8',
        };
    }

    /**
     * Điểm sàn tối thiểu mặc định để đạt cấp bậc
     */
    public function defaultMinPoints(): float
    {
        return match ($this) {
            self::NewMember => 0.0,
            self::Bronze => 20.0,
            self::Silver => 40.0,
            self::Gold => 60.0,
            self::Diamond => 80.0,
        };
    }

    /**
     * Lấy cấp bậc tiếp theo cần phấn đấu
     */
    public function getNextRank(): ?self
    {
        return match ($this) {
            self::NewMember => self::Bronze,
            self::Bronze => self::Silver,
            self::Silver => self::Gold,
            self::Gold => self::Diamond,
            self::Diamond => null,
        };
    }
}
