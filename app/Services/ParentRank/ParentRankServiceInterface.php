<?php

namespace App\Services\ParentRank;

use App\Enums\User\ParentRank;
use App\Models\ParentRankSnapshot;
use App\Models\User;

interface ParentRankServiceInterface
{
    /**
     * Lấy cấu hình phân hạng phụ huynh từ CSDL (bảng settings)
     *
     * @return array
     */
    public function getConfig(): array;

    /**
     * Thu thập các chỉ số thực tế trong tháng của một phụ huynh
     *
     * @param User $user
     * @param string $period Định dạng 'YYYY-MM'
     * @return array
     */
    public function collectMetrics(User $user, string $period): array;

    /**
     * Tính toán điểm thành phần, tổng điểm và cấp bậc đạt được
     *
     * @param array $metrics
     * @param array|null $config
     * @return array
     */
    public function computeScores(array $metrics, ?array $config = null): array;

    /**
     * Xác định cấp bậc phụ huynh dựa trên tổng điểm đạt được và cấu hình
     *
     * @param float $totalPoints
     * @param array|null $config
     * @return ParentRank
     */
    public function resolveRank(float $totalPoints, ?array $config = null): ParentRank;

    /**
     * Tính toán và lưu bản chụp xếp hạng của phụ huynh cho kỳ cụ thể
     *
     * @param User $user
     * @param string $period 'YYYY-MM'
     * @param bool $finalize Chốt sổ không tính lại
     * @return ParentRankSnapshot
     */
    public function calculateAndSave(User $user, string $period, bool $finalize = false): ParentRankSnapshot;

    /**
     * Tính toán xếp hạng cho toàn bộ người dùng trong kỳ
     *
     * @param string $period
     * @param bool $finalize
     * @param callable|null $onProgress
     * @return int Số lượng người dùng đã được cập nhật
     */
    public function calculateAll(string $period, bool $finalize = false, ?callable $onProgress = null): int;

    /**
     * Lấy thông tin tiến độ và hạng của người dùng kèm hướng dẫn phấn đấu
     *
     * @param User $user
     * @return array
     */
    public function getUserProgress(User $user): array;
}
