<?php

namespace App\Admin\Services\PostCategory;

use App\Admin\Services\PostCategory\PostCategoryServiceInterface;
use  App\Admin\Repositories\PostCategory\PostCategoryRepositoryInterface;
use Exception;
use Illuminate\Http\Request;

class PostCategoryService implements PostCategoryServiceInterface
{
    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;

    protected PostCategoryRepositoryInterface $repository;

    public function __construct(PostCategoryRepositoryInterface $repository){
        $this->repository = $repository;
    }

    public function store(Request $request){

        $this->data = $request->validated();

        return $this->repository->create($this->data);
    }

    /**
     * @throws Exception
     */
    public function update(Request $request): object|bool
    {

        $this->data = $request->validated();

        return $this->repository->update($this->data['id'], $this->data);

    }

    public function delete($id): object|bool
    {
        return $this->repository->delete($id);

    }

}
