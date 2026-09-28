<?php

namespace App\Admin\Http\Controllers\Lesson;

use App\Admin\DataTables\Lesson\LessonDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Lesson\CreateLessonRequest;
use App\Admin\Http\Requests\Lesson\UpdateLessonRequest;
use App\Admin\Repositories\Lesson\LessonRepositoryInterface;
use App\Admin\Services\File\FileService;
use App\Admin\Services\Lesson\LessonServiceInterface;
use App\Enums\ActiveStatus;
use App\Enums\Lesson\EducationPillar;
use App\Enums\Lesson\LessonAccessType;
use App\Enums\Lesson\LessonDifficulty;
use App\Models\AgeGroup;
use App\Models\Lesson;
use App\Models\LessonCategory;
use App\Models\LessonVideo;
use App\Traits\ResponseController;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Throwable;

class LessonController extends Controller
{
    use ResponseController;

    protected FileService $fileService;

    public function __construct(
        LessonRepositoryInterface $repository,
        LessonServiceInterface $service,
        FileService $fileService
    ) {
        parent::__construct();
        $this->repository = $repository;
        $this->service = $service;
        $this->fileService = $fileService;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.lessons.index',
            'create' => 'admin.lessons.create',
            'edit' => 'admin.lessons.edit',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.lesson.index',
            'create' => 'admin.lesson.create',
            'edit' => 'admin.lesson.edit',
            'delete' => 'admin.lesson.delete',
        ];
    }

    public function index(LessonDataTable $dataTable)
    {
        return $dataTable->render($this->view['index'], [
            'status' => ActiveStatus::asSelectArray(),
            'difficulties' => LessonDifficulty::asSelectArray(),
            'accessTypes' => LessonAccessType::asSelectArray(),
            'actionMultiple' => $this->getActionMultiple(),
            'breadcrumbs' => $this->crums->add(__('Bài học giáo dục')),
        ]);
    }

    public function create(): Factory|View|Application
    {
        $ageGroups = AgeGroup::active()->orderBy('sort_order')->get();
        $categories = LessonCategory::with('ageGroup')->active()->orderBy('sort_order')->get();

        return view($this->view['create'], [
            'ageGroups' => $ageGroups,
            'categories' => $categories,
            'pillars' => EducationPillar::cases(),
            'difficulties' => LessonDifficulty::cases(),
            'accessTypes' => LessonAccessType::asSelectArray(),
            'status' => ActiveStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add(__('Bài học giáo dục'), route($this->route['index']))->add(__('Thêm mới')),
        ]);
    }

    public function store(CreateLessonRequest $request): RedirectResponse
    {
        return $this->handleResponse($request, function ($request) {
            return $this->service->store($request);
        }, $this->route['index'], $this->route['edit']);
    }

    public function edit($id): Factory|View|Application
    {
        $instance = $this->repository->getQueryBuilderWithRelations(['category.ageGroup', 'videos'])
            ->findOrFail($id);

        $ageGroups = AgeGroup::active()->orderBy('sort_order')->get();
        $categories = LessonCategory::with('ageGroup')->active()->orderBy('sort_order')->get();

        return view($this->view['edit'], [
            'instance' => $instance,
            'ageGroups' => $ageGroups,
            'categories' => $categories,
            'pillars' => EducationPillar::cases(),
            'difficulties' => LessonDifficulty::cases(),
            'accessTypes' => LessonAccessType::asSelectArray(),
            'status' => ActiveStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add(__('Bài học giáo dục'), route($this->route['index']))->add(__('Chỉnh sửa')),
        ]);
    }

    public function update(UpdateLessonRequest $request): RedirectResponse
    {
        return $this->handleUpdateResponse($request, function () use ($request) {
            return $this->service->update($request);
        });
    }

    public function delete($id): RedirectResponse
    {
        return $this->handleDeleteResponse($id, function ($id) {
            return $this->service->delete($id);
        });
    }

    protected function getActionMultiple(): array
    {
        return ActiveStatus::asSelectArray();
    }

    public function actionMultipleRecords(Request $request): RedirectResponse
    {
        $boolean = $this->service->actionMultipleRecords($request);

        if ($boolean) {
            return back()->with('success', __('notifySuccess'));
        }

        return back()->with('error', __('notifyFail'));
    }

    /**
     * API AJAX lấy nhanh thông tin & thời lượng từ link YouTube
     */
    public function fetchYouTubeInfo(Request $request): JsonResponse
    {
        $url = $request->query('url', '');
        if (empty($url)) {
            return response()->json(['status' => false, 'message' => __('Đường dẫn video không hợp lệ.')]);
        }

        $ytId = LessonVideo::extractYouTubeId($url);
        if (!$ytId) {
            return response()->json(['status' => false, 'message' => __('Không thể nhận diện YouTube Video ID từ liên kết này.')]);
        }

        $durationSeconds = \App\Models\Video::extractYouTubeDuration($url);

        $title = '';
        $author = '';
        try {
            $response = Http::withoutVerifying()->timeout(6)->get("https://www.youtube.com/oembed?url=https://www.youtube.com/watch?v={$ytId}&format=json");
            if ($response->successful()) {
                $title = html_entity_decode($response->json('title') ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $author = html_entity_decode($response->json('author_name') ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
            } else {
                $fallback = Http::withoutVerifying()->timeout(6)->get("https://noembed.com/embed?url=https://www.youtube.com/watch?v={$ytId}");
                if ($fallback->successful()) {
                    $title = html_entity_decode($fallback->json('title') ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $author = html_entity_decode($fallback->json('author_name') ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
                }
            }
        } catch (Throwable $e) {
            try {
                $fallback = Http::withoutVerifying()->timeout(6)->get("https://noembed.com/embed?url=https://www.youtube.com/watch?v={$ytId}");
                if ($fallback->successful()) {
                    $title = html_entity_decode($fallback->json('title') ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $author = html_entity_decode($fallback->json('author_name') ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
                }
            } catch (Throwable $ex) {
                // Ignore fallback error
            }
        }

        $formattedDuration = null;
        if ($durationSeconds && $durationSeconds > 0) {
            $h = floor($durationSeconds / 3600);
            $m = floor(($durationSeconds % 3600) / 60);
            $s = $durationSeconds % 60;
            if ($h > 0) {
                $formattedDuration = sprintf('%02d:%02d:%02d (%d giờ %d phút %d giây)', $h, $m, $s, $h, $m, $s);
            } else {
                $formattedDuration = sprintf('%02d:%02d (%d phút %d giây)', $m, $s, $m, $s);
            }
        }

        return response()->json([
            'status' => true,
            'youtube_id' => $ytId,
            'title' => $title,
            'author' => $author,
            'duration_seconds' => $durationSeconds,
            'formatted_duration' => $formattedDuration,
            'thumbnail_url' => "https://img.youtube.com/vi/{$ytId}/hqdefault.jpg",
            'thumbnail_maxres' => "https://img.youtube.com/vi/{$ytId}/maxresdefault.jpg",
            'watch_url' => "https://www.youtube.com/watch?v={$ytId}",
            'embed_url' => "https://www.youtube.com/embed/{$ytId}",
        ]);
    }

    /**
     * API AJAX upload trực tiếp file video lên Cloudflare R2 (hỗ trợ thanh tiến trình % upload cho từng video trong repeater)
     */
    public function uploadR2Video(Request $request): JsonResponse
    {
        // Tăng giới hạn thời gian thực thi và bộ nhớ để hỗ trợ upload video dung lượng lớn (tối đa 200MB)
        set_time_limit(600);
        ini_set('max_execution_time', '600');
        ini_set('memory_limit', '512M');

        $request->validate([
            'video_file' => 'required|file|max:204800|mimes:mp4,mov,webm,mkv,avi,m4v',
        ], [
            'video_file.required' => 'Vui lòng chọn file video.',
            'video_file.file' => 'Dữ liệu tải lên phải là file video hợp lệ.',
            'video_file.max' => 'Dung lượng video tối đa là 200MB.',
            'video_file.mimes' => 'Định dạng video hợp lệ: mp4, mov, webm, mkv, avi, m4v.',
        ]);

        try {
            $uploadResult = $this->fileService->uploadVideoToR2(
                $request->file('video_file'),
                'videos/lessons'
            );

            return response()->json([
                'status' => true,
                'url' => $uploadResult['url'],
                'path' => $uploadResult['path'],
                'filename' => $request->file('video_file')->getClientOriginalName(),
                'size_mb' => round($request->file('video_file')->getSize() / (1024 * 1024), 2),
            ]);
        } catch (\Exception $e) {
            \Log::error('Upload R2 Video failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'status' => false,
                'message' => 'Lỗi upload lên Cloudflare R2: ' . $e->getMessage(),
            ], 500);
        }
    }
}
