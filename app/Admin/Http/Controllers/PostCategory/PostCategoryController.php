<?php

namespace App\Admin\Http\Controllers\PostCategory;

use App\Admin\DataTables\PostCategory\PostCategoryDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\PostCategory\PostCategoryRequest;
use App\Admin\Repositories\PostCategory\PostCategoryRepositoryInterface;
use App\Admin\Services\PostCategory\PostCategoryServiceInterface;
use App\Enums\PostCategory\PostCategoryStatus;
use App\Traits\ResponseController;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PostCategoryController extends Controller
{
    use ResponseController;

    public function __construct(
        PostCategoryRepositoryInterface $repository,
        PostCategoryServiceInterface    $service
    )
    {

        parent::__construct();

        $this->repository = $repository;

        $this->service = $service;

    }

    public function getView(): array
    {
        return [
            'index' => 'admin.posts_categories.index',
            'create' => 'admin.posts_categories.create',
            'edit' => 'admin.posts_categories.edit'
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.post_category.index',
            'create' => 'admin.post_category.create',
            'edit' => 'admin.post_category.edit',
            'delete' => 'admin.post_category.delete'
        ];
    }

    public function index(PostCategoryDataTable $dataTable)
    {
        return $dataTable->render($this->view['index'], [
            'breadcrumbs' => $this->crums->add(__('post_categories'),)
        ]);
    }

    public function create(): Factory|View|Application
    {
        $categories = $this->repository->getFlatTree();

        return view($this->view['create'], [
            'categories' => $categories,
            'status' => PostCategoryStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add(__('post_categories'), route($this->route['index']))->add(__('add'))
        ]);
    }

    public function store(PostCategoryRequest $request): RedirectResponse
    {
        return $this->handleResponse($request, function ($request) {
            return $this->service->store($request);
        }, $this->route['index'], $this->route['edit']);

    }

    /**
     * @throws Exception
     */
    public function edit($id): Factory|View|Application
    {
        $categories = $this->repository->getFlatTreeNotInNode([$id]);

        $category = $this->repository->findOrFail($id);
        return view(
            $this->view['edit'],
            [
                'category' => $category,
                'categories' => $categories,
                'status' => PostCategoryStatus::asSelectArray(),
                'breadcrumbs' => $this->crums->add(__('post_categories'), route($this->route['index']))->add(__('edit'))

            ],
        );

    }

    public function update(PostCategoryRequest $request): RedirectResponse
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
}
