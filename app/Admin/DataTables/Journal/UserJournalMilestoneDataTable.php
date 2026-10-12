<?php

namespace App\Admin\DataTables\Journal;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Journal\JournalMilestoneRepositoryInterface;
use App\Enums\Journal\MilestoneStage;
use App\Enums\Permission\PermissionType;
use Illuminate\Database\Eloquent\Builder;

class UserJournalMilestoneDataTable extends BaseDataTable
{
    protected $nameTable = 'UserJournalMilestoneTable';

    public function __construct(JournalMilestoneRepositoryInterface $repository)
    {
        $this->repository = $repository;
        parent::__construct();
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.journal_milestones.user_datatable.action',
            'stage' => 'admin.journal_milestones.datatable.stage',
            'child' => 'admin.journal_milestones.user_datatable.child',
            'user' => 'admin.journal_milestones.user_datatable.user',
            'name' => 'admin.journal_milestones.user_datatable.name',
        ];
    }

    public function setColumnSearch(): void
    {
        $this->columnAllSearch = [0, 1, 2, 3];
    }

    public function query(): Builder
    {
        $stage = request('stage');
        $query = $this->repository->getQueryBuilderOrderBy('id', 'desc')
            ->where('type', PermissionType::USER)
            ->with(['child', 'user'])
            ->withCount('journals');

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
            'child' => [
                'title' => '<div class="header-cell-content"><i class="ti ti-baby-carriage"></i><span>Bé sở hữu</span></div>',
                'addClass' => 'align-middle',
                'orderable' => false,
            ],
            'user' => [
                'title' => '<div class="header-cell-content"><i class="ti ti-user"></i><span>Phụ huynh</span></div>',
                'addClass' => 'align-middle',
                'orderable' => false,
            ],
            'journals_count' => [
                'title' => '<div class="header-cell-content"><i class="ti ti-camera"></i><span>Nhật ký</span></div>',
                'addClass' => 'text-center align-middle',
                'orderable' => true,
            ],
            'created_at' => [
                'title' => '<div class="header-cell-content"><i class="ti ti-calendar-event"></i><span>Ngày tạo</span></div>',
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
            'child' => function ($query, $keyword) {
                $query->whereHas('child', function ($subQuery) use ($keyword) {
                    $subQuery->where('fullname', 'like', "%{$keyword}%");
                });
            },
            'user' => function ($query, $keyword) {
                $query->whereHas('user', function ($subQuery) use ($keyword) {
                    $subQuery->where('fullname', 'like', "%{$keyword}%")
                        ->orWhere('phone', 'like', "%{$keyword}%");
                });
            },
        ];
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'name' => function ($milestone) {
                return view($this->view['name'], ['milestone' => $milestone])->render();
            },
            'stage' => function ($milestone) {
                return view($this->view['stage'], ['milestone' => $milestone])->render();
            },
            'child' => function ($milestone) {
                return view($this->view['child'], ['child' => $milestone->child])->render();
            },
            'user' => function ($milestone) {
                return view($this->view['user'], ['user' => $milestone->user])->render();
            },
            'journals_count' => function ($milestone) {
                $count = $milestone->journals_count ?? 0;
                if ($count > 0) {
                    return '<span class="badge bg-primary-lt px-2 py-1"><i class="ti ti-check me-1"></i>' . $count . ' khoảnh khắc</span>';
                }
                return '<span class="badge bg-secondary-lt px-2 py-1 text-muted">Chưa ghi nhận</span>';
            },
            'created_at' => function ($milestone) {
                return $milestone->created_at ? format_datetime($milestone->created_at) : '—';
            },
            'action' => function ($milestone) {
                return view($this->view['action'], ['id' => $milestone->id])->render();
            },
        ];
    }
}
