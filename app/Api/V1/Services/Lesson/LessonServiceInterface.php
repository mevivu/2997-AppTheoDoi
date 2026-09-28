<?php

namespace App\Api\V1\Services\Lesson;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

interface LessonServiceInterface
{
    /**
     * Kiểm tra user hiện tại có gói VIP đang hoạt động hay không
     */
    public function isVipUser(): bool;

    /**
     * Lấy danh sách nhóm độ tuổi (tự động phát hiện nhóm tuổi của bé nếu truyền child_id/child_age_months)
     */
    public function getAgeGroups(Request $request): Collection;

    /**
     * Lấy danh sách 5 trụ cột giáo dục kèm thống kê số bài học
     */
    public function getPillars(Request $request): array;

    /**
     * Lấy danh mục bài học / kỹ năng theo nhóm tuổi & trụ cột
     */
    public function getCategories(Request $request): Collection;

    /**
     * Lấy danh sách bài học phân trang kèm quyền truy cập Free/VIP
     */
    public function getLessonList(Request $request): array;

    /**
     * Lấy chi tiết bài học và danh sách bài học liên quan
     */
    public function getLessonDetail(int $id): array;

    /**
     * Tăng lượt xem bài học kèm bảo vệ chống spam (2 giờ / user)
     */
    public function incrementView(Request $request, int $id): array;
}
