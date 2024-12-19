<?php

namespace App\Admin\Services\Journal;

use App\Admin\Repositories\Journal\JournalRepositoryInterface;
use App\Admin\Traits\Roles;
use App\Api\V1\Support\UseLog;
use Exception;
use Illuminate\Http\Request;
use App\Admin\Traits\Setup;

class JournalService implements JournalServiceInterface
{
    use Setup, Roles, UseLog;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;

    protected JournalRepositoryInterface $repository;


    public function __construct(
        JournalRepositoryInterface $repository,
    )
    {
        $this->repository = $repository;
    }

    /**
     * @throws Exception
     */
    public function store(Request $request): object|false
    {
        $data = $request->validated();
        $data['image'] = $data['image'] ? json_encode(explode(",", $data['image'][0])) : null;

        return $this->repository->create($data);
    }


    /**
     * @throws Exception
     */
    public function update(Request $request): object|bool
    {

        $data = $request->validated();
        $data['image'] = $data['image'] ? json_encode(explode(",", $data['image'][0])) : null;

        return $this->repository->update($data['id'], $data);
    }

    /**
     * @throws Exception
     */
    public function delete($id): object
    {
        return $this->repository->delete($id);

    }
}
