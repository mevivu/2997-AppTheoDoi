<?php

namespace App\Admin\Http\Controllers\Video;

use App\Admin\DataTables\Video\VideoDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Video\VideoRequest;
use App\Admin\Repositories\Video\VideoRepositoryInterface;
use App\Admin\Services\Video\VideoServiceInterface;
use App\Enums\ActiveStatus;
use App\Enums\Video\VideoAccessType;
use App\Models\VideoCategory;
use App\Traits\ResponseController;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class VideoController extends Controller
{
    use ResponseController;

    public function __construct(
        VideoRepositoryInterface $repository,
        VideoServiceInterface $service
    ) {
        parent::__construct();
        $this->repository = $repository;
        $this->service = $service;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.videos.index',
            'create' => 'admin.videos.create',
            'edit' => 'admin.videos.edit',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.video.index',
            'create' => 'admin.video.create',
            'edit' => 'admin.video.edit',
            'delete' => 'admin.video.delete',
        ];
    }

    public function index(VideoDataTable $dataTable)
    {
        return $dataTable->render($this->view['index'], [
            'status' => ActiveStatus::asSelectArray(),
            'accessTypes' => VideoAccessType::asSelectArray(),
            'actionMultiple' => $this->getActionMultiple(),
            'breadcrumbs' => $this->crums->add(__('Video giáo dục')),
        ]);
    }

    public function create(): Factory|View|Application
    {
        $categories = VideoCategory::with('ageGroup')->active()->orderBy('sort_order')->get();
        $categoriesByAge = $categories->groupBy(fn($c) => $c->ageGroup?->name ?? __('Chung'));

        return view($this->view['create'], [
            'categories' => $categories,
            'categoriesByAge' => $categoriesByAge,
            'accessTypes' => VideoAccessType::asSelectArray(),
            'status' => ActiveStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add(__('Video giáo dục'), route($this->route['index']))->add(__('Thêm mới')),
        ]);
    }

    public function store(VideoRequest $request): RedirectResponse
    {
        return $this->handleResponse($request, function ($request) {
            return $this->service->store($request);
        }, $this->route['index'], $this->route['edit']);
    }

    public function edit($id): Factory|View|Application
    {
        $instance = $this->repository->findOrFail($id);
        $categories = VideoCategory::with('ageGroup')->active()->orderBy('sort_order')->get();
        $categoriesByAge = $categories->groupBy(fn($c) => $c->ageGroup?->name ?? __('Chung'));

        return view($this->view['edit'], [
            'instance' => $instance,
            'categories' => $categories,
            'categoriesByAge' => $categoriesByAge,
            'accessTypes' => VideoAccessType::asSelectArray(),
            'status' => ActiveStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add(__('Video giáo dục'), route($this->route['index']))->add(__('Chỉnh sửa')),
        ]);
    }

    public function update(VideoRequest $request): RedirectResponse
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
     * API AJAX lấy nhanh thông tin & thời lượng từ liên kết YouTube
     */
    public function fetchYouTubeInfo(Request $request): JsonResponse
    {
        $url = $request->query('url', '');
        if (empty($url)) {
            return response()->json(['status' => false, 'message' => __('Đường dẫn video không hợp lệ.')]);
        }

        $ytId = \App\Models\Video::extractYouTubeId($url);
        if (!$ytId) {
            return response()->json(['status' => false, 'message' => __('Không thể nhận diện YouTube Video ID từ liên kết này.')]);
        }

        $durationSeconds = \App\Models\Video::extractYouTubeDuration($url);

        // Lấy tiêu đề và tác giả qua YouTube oEmbed (ưu tiên YouTube chính thức, dự phòng noembed)
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
        } catch (\Throwable $e) {
            try {
                $fallback = Http::withoutVerifying()->timeout(6)->get("https://noembed.com/embed?url=https://www.youtube.com/watch?v={$ytId}");
                if ($fallback->successful()) {
                    $title = html_entity_decode($fallback->json('title') ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $author = html_entity_decode($fallback->json('author_name') ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
                }
            } catch (\Throwable $ex) {
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
}
