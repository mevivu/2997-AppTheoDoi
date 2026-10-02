<?php

namespace App\Admin\Http\Controllers\KnowledgePost;

use App\Admin\DataTables\KnowledgePost\KnowledgePostDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\KnowledgePost\KnowledgePostRequest;
use App\Admin\Repositories\Post\PostRepositoryInterface;
use App\Admin\Services\KnowledgePost\KnowledgePostServiceInterface;
use App\Enums\FeaturedStatus;
use App\Enums\Post\PostStatus;
use App\Traits\ResponseController;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class KnowledgePostController extends Controller
{
    use ResponseController;

    public function __construct(
        PostRepositoryInterface $repository,
        KnowledgePostServiceInterface $service
    ) {
        parent::__construct();

        $this->repository = $repository;
        $this->service = $service;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.knowledge_posts.index',
            'create' => 'admin.knowledge_posts.create',
            'edit' => 'admin.knowledge_posts.edit'
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.knowledge_post.index',
            'create' => 'admin.knowledge_post.create',
            'edit' => 'admin.knowledge_post.edit',
            'delete' => 'admin.knowledge_post.delete'
        ];
    }

    public function index(KnowledgePostDataTable $dataTable)
    {
        $actionMultiple = $this->getActionMultiple();
        return $dataTable->render($this->view['index'], [
            'status' => PostStatus::asSelectArray(),
            'actionMultiple' => $actionMultiple,
            'breadcrumbs' => $this->crums->add('Kiến thức chăm con'),
        ]);
    }

    public function create(): Factory|View|Application
    {
        return view($this->view['create'], [
            'status' => PostStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add('Kiến thức chăm con', route($this->route['index']))->add('Thêm mới')
        ]);
    }

    public function store(KnowledgePostRequest $request): RedirectResponse
    {
        return $this->handleResponse($request, function ($request) {
            return $this->service->store($request);
        }, $this->route['index'], $this->route['edit']);
    }

    public function edit($id): Factory|View|Application
    {
        $post = $this->repository->findOrFail($id);
        return view(
            $this->view['edit'],
            [
                'post' => $post,
                'status' => PostStatus::asSelectArray(),
                'featured_status' => FeaturedStatus::asSelectArray(),
                'breadcrumbs' => $this->crums->add('Kiến thức chăm con', route($this->route['index']))->add('Chỉnh sửa')
            ]
        );
    }

    public function update(KnowledgePostRequest $request): RedirectResponse
    {
        return $this->handleUpdateResponse($request, function ($request) {
            return $this->service->update($request);
        });
    }

    public function delete($id): RedirectResponse
    {
        $this->service->delete($id);
        return to_route($this->route['index'])->with('success', __('notifySuccess'));
    }

    protected function getActionMultiple(): array
    {
        return [
            1 => PostStatus::Published->description(),
            2 => PostStatus::Draft->description(),
        ];
    }

    public function actionMultipleRecode(Request $request): RedirectResponse
    {
        $boolean = $this->service->actionMultipleRecode($request);
        if ($boolean) {
            return back()->with('success', __('notifySuccess'));
        }
        return back()->with('error', __('notifyFail'));
    }
}
