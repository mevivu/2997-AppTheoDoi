<?php

namespace App\Admin\DataTables\Journal;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Journal\JournalMilestoneRepositoryInterface;
use App\Enums\Journal\MilestoneStage;
use App\Enums\Permission\PermissionType;
use Illuminate\Database\Eloquent\Builder;

class JournalMilestoneDataTable extends BaseDataTable
{
    protected $nameTable = 'JournalMilestoneTable';

    public function __construct(JournalMilestoneRepositoryInterface $repository)
    {
        $this->repository = $repository;
        parent::__construct();
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.journal_milestones.datatable.action',
            'stage' => 'admin.journal_milestones.datatable.stage',
            'is_active' => 'admin.journal_milestones.datatable.is_active',
        ];
    }

    public function setColumnSearch(): void
    {
        $this->columnAllSearch = [0, 1];
    }

    public function query(): Builder
    {
        $stage = request('stage');
        $query = $this->repository->getQueryBuilderOrderBy('position', 'asc')
            ->where('type', PermissionType::ADMIN);

        if ($stage && in_array($stage, MilestoneStage::getValues())) {
            $query->where('stage', $stage);
        }

        return $query;
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = [
            'name' => [
                'title' => '<div class="header-cell-content"><i class="ti ti-flag"></i><span>Tên cột mốc</span></div>',
                'addClass' => 'align-middle',
                'orderable' => true,
            ],
            'stage' => [
                'title' => '<div class="header-cell-content"><i class="ti ti-category"></i><span>Giai đoạn</span></div>',
                'addClass' => 'text-center align-middle',
                'orderable' => true,
            ],
            'age_range' => [
                'title' => '<div class="header-cell-content"><i class="ti ti-calendar-time"></i><span>Độ tuổi (tháng)</span></div>',
                'addClass' => 'text-center align-middle',
                'orderable' => false,
            ],
            'position' => [
                'title' => '<div class="header-cell-content"><i class="ti ti-arrows-sort"></i><span>Thứ tự</span></div>',
                'addClass' => 'text-center align-middle',
                'orderable' => true,
            ],
            'is_active' => [
                'title' => '<div class="header-cell-content"><i class="ti ti-info-circle"></i><span>Trạng thái</span></div>',
                'addClass' => 'text-center align-middle',
                'orderable' => true,
            ],
            'action' => [
                'title' => '<div class="header-cell-content"><i class="ti ti-settings"></i><span>Thao tác</span></div>',
                'orderable' => false,
                'exportable' => false,
                'printable' => false,
                'addClass' => 'text-center align-middle',
            ],
        ];
    }

    public function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [
            'name' => function ($query, $keyword) {
                $query->where('name', 'like', "%{$keyword}%");
            },
        ];
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'stage' => function ($milestone) {
                return view($this->view['stage'], ['milestone' => $milestone])->render();
            },
            'age_range' => function ($milestone) {
                return "{$milestone->age_from_month} – {$milestone->age_to_month} tháng";
            },
            'is_active' => function ($milestone) {
                return view($this->view['is_active'], ['milestone' => $milestone])->render();
            },
        ];
    }

    protected function setCustomAddColumns(): void
    {
        $this->customAddColumns = [
            'action' => $this->view['action'],
        ];
    }

    protected function setCustomRawColumns(): void
    {
        $this->customRawColumns = [
            'stage',
            'is_active',
            'action',
        ];
    }
}
