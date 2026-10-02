<?php

namespace App\Admin\DataTables\ExpertPost;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\ExpertPost\ExpertPostRepositoryInterface;
use App\Admin\Traits\GetConfig;
use App\Enums\DefaultStatus;
use App\Models\AgeGroup;
use App\Models\Expert;
use App\Models\ExpertCategory;

class ExpertPostDataTable extends BaseDataTable
{
    use GetConfig;

    protected $nameTable = 'ExpertPostTable';
    protected array $actions = ['reset', 'reload'];

    public function __construct(ExpertPostRepositoryInterface $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.expert_posts.datatable.action',
            'image' => 'admin.expert_posts.datatable.image',
            'editlink' => 'admin.expert_posts.datatable.editlink',
            'expert' => 'admin.expert_posts.datatable.expert',
            'category' => 'admin.expert_posts.datatable.category',
            'age_group' => 'admin.expert_posts.datatable.age-group',
            'is_featured' => 'admin.expert_posts.datatable.is-featured',
            'status' => 'admin.expert_posts.datatable.status',
            'checkbox' => 'admin.common.checkbox',
        ];
    }

    public function setColumnSearch(): void
    {
        $expertOptions = Expert::query()->orderBy('name')->pluck('name', 'id')->all();
        $categoryOptions = ExpertCategory::query()->orderBy('name')->pluck('name', 'id')->all();
        $ageGroupOptions = AgeGroup::query()->orderBy('sort_order')->pluck('name', 'id')->all();

        $this->columnAllSearch = [2, 3, 4, 5, 6, 8];
        $this->columnSearchSelect = [
            [
                'column' => 3, // Expert
                'data' => $expertOptions,
            ],
            [
                'column' => 4, // Category
                'data' => $categoryOptions,
            ],
            [
                'column' => 5, // Age Group
                'data' => $ageGroupOptions,
            ],
            [
                'column' => 6, // Is Featured
                'data' => [1 => 'Nổi bật', 0 => 'Thường'],
            ],
            [
                'column' => 8, // Status
                'data' => [
                    DefaultStatus::Published->value => 'Xuất bản',
                    DefaultStatus::Draft->value => 'Bản nháp',
                ],
            ],
        ];
    }

    public function query()
    {
        return $this->repository->getQueryBuilder()
            ->with(['expert', 'category', 'ageGroup'])
            ->orderBy('id', 'desc');
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.expert_post', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'image' => $this->view['image'],
            'title' => $this->view['editlink'],
            'expert' => $this->view['expert'],
            'category' => $this->view['category'],
            'age_group' => $this->view['age_group'],
            'is_featured' => $this->view['is_featured'],
            'status' => $this->view['status'],
        ];
    }

    protected function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [
            'expert' => function ($query, $keyword) {
                $query->where('expert_id', $keyword);
            },
            'category' => function ($query, $keyword) {
                $query->where('category_id', $keyword);
            },
            'age_group' => function ($query, $keyword) {
                $query->where('age_group_id', $keyword);
            },
            'is_featured' => function ($query, $keyword) {
                $query->where('is_featured', $keyword);
            },
        ];
    }

    protected function setCustomAddColumns(): void
    {
        $this->customAddColumns = [
            'action' => $this->view['action'],
            'checkbox' => $this->view['checkbox'],
        ];
    }

    protected function setCustomRawColumns(): void
    {
        $this->customRawColumns = ['image', 'title', 'expert', 'category', 'age_group', 'is_featured', 'status', 'action', 'checkbox'];
    }
}
