<?php

namespace App\Admin\DataTables\VideoCategory;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\VideoCategory\VideoCategoryRepositoryInterface;
use App\Enums\ActiveStatus;
use App\Models\AgeGroup;

class VideoCategoryDataTable extends BaseDataTable
{
    protected $nameTable = 'videoCategoryTable';

    public function __construct(VideoCategoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
        parent::__construct();
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.video_categories.datatable.action',
            'status' => 'admin.video_categories.datatable.status',
            'icon' => 'admin.video_categories.datatable.icon',
            'checkbox' => 'admin.common.checkbox',
        ];
    }

    public function setColumnSearch(): void
    {
        $ageGroupOptions = AgeGroup::query()
            ->orderBy('sort_order')
            ->pluck('name', 'id')
            ->all();

        $this->columnAllSearch = [2, 3, 4, 5];
        $this->columnSearchSelect = [
            [
                'column' => 3,
                'data' => $ageGroupOptions,
            ],
            [
                'column' => 5,
                'data' => ActiveStatus::asSelectArray(),
            ],
        ];
    }

    public function query()
    {
        return $this->repository->getQueryBuilderWithRelations(['ageGroup'])
            ->orderBy('sort_order', 'asc');
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.video_category', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'icon' => $this->view['icon'],
            'age_group' => fn($row) => $row->ageGroup?->name ?? '—',
            'status' => $this->view['status'],
        ];
    }

    protected function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [
            'age_group' => function ($query, $keyword) {
                $query->where('age_group_id', $keyword);
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
        $this->customRawColumns = ['icon', 'action', 'status', 'checkbox'];
    }
}
