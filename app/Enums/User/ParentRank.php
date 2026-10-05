<?php

namespace App\Enums\User;

use App\Admin\Support\Enum;

/**
 * Cấp Bậc Phụ Huynh (Parent Rank)
 *
 * Phân hạng thành viên phụ huynh gồm 4 cấp:
 * - Hạng A: Bố mẹ gương mẫu (từ 80 điểm)
 * - Hạng B: Bố mẹ tích cực (từ 60 điểm)
 * - Hạng C: Bố mẹ đang cố gắng (từ 40 điểm)
 * - Hạng D: Bố mẹ cần hỗ trợ thêm (dưới 40 điểm)
 */
enum ParentRank: int
{
    use Enum;

    /** Hạng D: Bố mẹ cần hỗ trợ thêm - Dưới 40 điểm */
    case D = 0;

    /** Hạng C: Bố mẹ đang cố gắng - Từ 40 điểm */
    case C = 1;

    /** Hạng B: Bố mẹ tích cực - Từ 60 điểm */
    case B = 2;

    /** Hạng A: Bố mẹ gương mẫu - Từ 80 điểm */
    case A = 3;

    /**
     * Tên đầy đủ hiển thị của Cấp bậc
     */
    public function name(): string
    {
        return match ($this) {
            self::D => 'Hạng D – Bố mẹ cần hỗ trợ thêm',
            self::C => 'Hạng C – Bố mẹ đang cố gắng',
            self::B => 'Hạng B – Bố mẹ tích cực',
            self::A => 'Hạng A – Bố mẹ gương mẫu',
        };
    }

    /**
     * Tên ngắn gọn của cấp bậc (Hạng A, Hạng B, Hạng C, Hạng D)
     */
    public function shortName(): string
    {
        return match ($this) {
            self::D => 'Hạng D',
            self::C => 'Hạng C',
            self::B => 'Hạng B',
            self::A => 'Hạng A',
        };
    }

    /**
     * Tên hiển thị (Alias của name())
     */
    public function label(): string
    {
        return $this->name();
    }

    /**
     * Thông điệp truyền cảm hứng cho từng hạng
     */
    public function description(): string
    {
        return match ($this) {
            self::D => 'Đừng lo! Mỗi hành trình đều có bước khởi đầu. Hãy bắt đầu từ hôm nay – Chăm con 360 sẽ luôn đồng hành cùng bạn!',
            self::C => 'Bạn đang đi đúng hướng – chỉ cần thêm một chút kiên trì, hành trình nuôi dạy con sẽ trở nên dễ dàng và hiệu quả hơn.',
            self::B => 'Bạn đang làm rất tốt! Chỉ cần một chút đều đặn hơn nữa là sẽ đạt đến hạng A.',
            self::A => 'Bạn chính là hình mẫu lý tưởng của sự nuôi dạy con có trách nhiệm và khoa học.',
        };
    }

    /**
     * Class CSS màu nền badge giao diện Admin (Tabler UI)
     */
    public function badge(): string
    {
        return match ($this) {
            self::D => 'bg-muted-lt',
            self::C => 'bg-orange-lt',
            self::B => 'bg-cyan-lt',
            self::A => 'bg-yellow-lt',
        };
    }

    /**
     * Tên class icon Tabler Icons
     */
    public function icon(): string
    {
        return match ($this) {
            self::D => 'ti ti-mood-smile',
            self::C => 'ti ti-shield',
            self::B => 'ti ti-medal',
            self::A => 'ti ti-crown',
        };
    }

    /**
     * Mã màu hex đặc trưng cho cấp bậc
     */
    public function colorHex(): string
    {
        return match ($this) {
            self::D => '#94A3B8',
            self::C => '#F59E0B',
            self::B => '#0284C7',
            self::A => '#EAB308',
        };
    }

    /**
     * Điểm sàn tối thiểu mặc định để đạt cấp bậc
     */
    public function defaultMinPoints(): float
    {
        return match ($this) {
            self::D => 0.0,
            self::C => 40.0,
            self::B => 60.0,
            self::A => 80.0,
        };
    }

    /**
     * Lấy cấp bậc tiếp theo cần phấn đấu
     */
    public function getNextRank(): ?self
    {
        return match ($this) {
            self::D => self::C,
            self::C => self::B,
            self::B => self::A,
            self::A => null,
        };
    }
}
