<?php

namespace App\Admin\Http\Controllers\Lesson;

use App\Admin\DataTables\Lesson\LessonRatingDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Repositories\Lesson\LessonRepositoryInterface;
use App\Enums\Lesson\LessonDifficultyRating;
use App\Models\AgeGroup;
use App\Models\Lesson;
use App\Models\LessonDifficultyRatingModel;
use App\Traits\ResponseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class LessonRatingController extends Controller
{
    use ResponseController;

    public function __construct(LessonRepositoryInterface $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.lesson-ratings.index',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.lesson_rating.index',
        ];
    }

    /**
     * Hiển thị trang thống kê đánh giá độ khó bài học
     */
    public function index(LessonRatingDataTable $dataTable)
    {
        // Thống kê tổng quan cho Dashboard Cards
        $totalRatings = LessonDifficultyRatingModel::query()->count();

        $easyCount = LessonDifficultyRatingModel::query()
            ->where('difficulty_level', LessonDifficultyRating::Easy->value)
            ->count();

        $withHelpCount = LessonDifficultyRatingModel::query()
            ->where('difficulty_level', LessonDifficultyRating::WithHelp->value)
            ->count();

        $hardCount = LessonDifficultyRatingModel::query()
            ->where('difficulty_level', LessonDifficultyRating::Hard->value)
            ->count();

        $easyPct = $totalRatings > 0 ? round(($easyCount / $totalRatings) * 100, 1) : 0;
        $withHelpPct = $totalRatings > 0 ? round(($withHelpCount / $totalRatings) * 100, 1) : 0;
        $hardPct = $totalRatings > 0 ? round(($hardCount / $totalRatings) * 100, 1) : 0;

        $ratedLessonsCount = LessonDifficultyRatingModel::query()
            ->distinct('lesson_id')
            ->count('lesson_id');

        $totalLessons = Lesson::query()->count();

        $stats = [
            'total_ratings' => $totalRatings,
            'easy_count' => $easyCount,
            'easy_pct' => $easyPct,
            'with_help_count' => $withHelpCount,
            'with_help_pct' => $withHelpPct,
            'hard_count' => $hardCount,
            'hard_pct' => $hardPct,
            'rated_lessons_count' => $ratedLessonsCount,
            'total_lessons' => $totalLessons,
        ];

        return $dataTable->render($this->view['index'], [
            'stats' => $stats,
            'breadcrumbs' => $this->crums
                ->add(__('Bài học giáo dục'), route('admin.lesson.index'))
                ->add(__('Thống kê đánh giá')),
        ]);
    }

    /**
     * API lấy chi tiết đánh giá của 1 bài học (cho Modal AJAX)
     */
    public function detail(int $id): JsonResponse
    {
        try {
            $lesson = Lesson::query()
                ->with(['ageGroup'])
                ->findOrFail($id);

            // Thống kê đánh giá của bài học này
            $total = LessonDifficultyRatingModel::query()
                ->where('lesson_id', $id)
                ->count();

            $easyCount = LessonDifficultyRatingModel::query()
                ->where('lesson_id', $id)
                ->where('difficulty_level', LessonDifficultyRating::Easy->value)
                ->count();

            $withHelpCount = LessonDifficultyRatingModel::query()
                ->where('lesson_id', $id)
                ->where('difficulty_level', LessonDifficultyRating::WithHelp->value)
                ->count();

            $hardCount = LessonDifficultyRatingModel::query()
                ->where('lesson_id', $id)
                ->where('difficulty_level', LessonDifficultyRating::Hard->value)
                ->count();

            // Danh sách đánh giá chi tiết gần nhất (50 bản ghi)
            $ratings = LessonDifficultyRatingModel::query()
                ->where('lesson_id', $id)
                ->with([
                    'user:id,fullname,phone,avatar',
                    'child:id,fullname,avatar',
                ])
                ->latest()
                ->limit(50)
                ->get()
                ->map(function ($item) {
                    $levelEnum = $item->difficulty_level;

                    // Giải mã SĐT phụ huynh an toàn bằng AESHelper / accessor decrypted_phone
                    $phone = $item->user?->decrypted_phone;
                    if (empty($phone) && !empty($item->user?->phone)) {
                        try {
                            $decrypted = \App\AES\AESHelper::decrypt($item->user->phone);
                            $phone = ($decrypted !== false && !empty($decrypted)) ? $decrypted : $item->user->phone;
                        } catch (\Throwable $e) {
                            $phone = $item->user->phone;
                        }
                    }

                    return [
                        'id' => $item->id,
                        'user_name' => $item->user?->fullname ?? 'Phụ huynh',
                        'user_phone' => !empty($phone) ? $phone : 'Chưa cập nhật',
                        'user_avatar' => $item->user?->avatar ? asset($item->user->avatar) : null,
                        'child_name' => $item->child?->fullname ?? 'Bé',
                        'child_avatar' => $item->child?->avatar ? asset($item->child->avatar) : null,
                        'difficulty_level' => $levelEnum instanceof LessonDifficultyRating ? $levelEnum->value : $levelEnum,
                        'difficulty_label' => $levelEnum instanceof LessonDifficultyRating ? $levelEnum->label() : ($levelEnum ?? '—'),
                        'difficulty_icon' => $levelEnum instanceof LessonDifficultyRating ? $levelEnum->icon() : '❓',
                        'difficulty_badge' => $levelEnum instanceof LessonDifficultyRating ? $levelEnum->badge() : 'bg-secondary-lt',
                        'created_at' => $item->created_at ? $item->created_at->format('d/m/Y H:i') : '—',
                    ];
                });

            return response()->json([
                'status' => 200,
                'message' => 'Lấy dữ liệu thành công',
                'data' => [
                    'lesson' => [
                        'id' => $lesson->id,
                        'name' => $lesson->name,
                        'age_group' => $lesson->ageGroup?->name ?? '—',
                        'thumbnail' => $lesson->thumbnail_url,
                    ],
                    'summary' => [
                        'total' => $total,
                        'easy' => [
                            'count' => $easyCount,
                            'pct' => $total > 0 ? round(($easyCount / $total) * 100, 1) : 0,
                            'label' => LessonDifficultyRating::Easy->label(),
                            'icon' => LessonDifficultyRating::Easy->icon(),
                        ],
                        'with_help' => [
                            'count' => $withHelpCount,
                            'pct' => $total > 0 ? round(($withHelpCount / $total) * 100, 1) : 0,
                            'label' => LessonDifficultyRating::WithHelp->label(),
                            'icon' => LessonDifficultyRating::WithHelp->icon(),
                        ],
                        'hard' => [
                            'count' => $hardCount,
                            'pct' => $total > 0 ? round(($hardCount / $total) * 100, 1) : 0,
                            'label' => LessonDifficultyRating::Hard->label(),
                            'icon' => LessonDifficultyRating::Hard->icon(),
                        ],
                    ],
                    'ratings' => $ratings,
                ],
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy bài học hoặc có lỗi xảy ra',
            ], 404);
        }
    }
}
