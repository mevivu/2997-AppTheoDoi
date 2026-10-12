<?php

namespace App\Admin\Services\Journal;

use App\Admin\Repositories\Journal\JournalMilestoneRepositoryInterface;
use App\Admin\Traits\Roles;
use App\Admin\Traits\Setup;
use App\Api\V1\Support\UseLog;
use App\Models\JournalMilestone;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class JournalMilestoneService implements JournalMilestoneServiceInterface
{
    use Setup, Roles, UseLog;

    protected JournalMilestoneRepositoryInterface $repository;

    public function __construct(JournalMilestoneRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @throws Exception
     */
    public function store(Request $request): object|false
    {
        $data = $request->validated();

        if (empty($data['slug'])) {
            $baseSlug = Str::slug($data['name']);
            $slug = $baseSlug;
            $counter = 1;
            while (JournalMilestone::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }
            $data['slug'] = $slug;
        }

        $data['is_active'] = $request->boolean('is_active', true);
        $data['position'] = $data['position'] ?? 0;

        return $this->repository->create($data);
    }

    /**
     * @throws Exception
     */
    public function update(Request $request): object|bool
    {
        $data = $request->validated();
        $id = $data['id'];

        if (empty($data['slug'])) {
            $baseSlug = Str::slug($data['name']);
            $slug = $baseSlug;
            $counter = 1;
            while (JournalMilestone::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }
            $data['slug'] = $slug;
        }

        $data['is_active'] = $request->boolean('is_active');
        $data['position'] = $data['position'] ?? 0;

        return $this->repository->update($id, $data);
    }

    /**
     * @throws Exception
     */
    public function delete($id): bool
    {
        $milestone = $this->repository->findOrFail($id);

        if ($milestone->journals()->exists()) {
            throw new Exception('Không thể xoá cột mốc này vì đã có nhật ký của trẻ tham chiếu. Vui lòng chuyển trạng thái sang "Tắt" thay vì xoá để đảm bảo tính toàn vẹn dữ liệu.');
        }

        return (bool)$this->repository->delete($id);
    }
}
