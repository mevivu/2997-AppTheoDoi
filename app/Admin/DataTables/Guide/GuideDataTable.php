<?php

namespace App\Admin\DataTables\Guide;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Guide\GuideRepositoryInterface;
use App\Enums\ActiveStatus;
use App\Enums\Guide\GuideType;
use Illuminate\Database\Eloquent\Builder;

class GuideDataTable extends BaseDataTable
{
    protected $nameTable = 'guideTable';

    protected array $actions = ['reset', 'reload'];

    public function __construct(
        GuideRepositoryInterface $repository
    ) {
        $this->repository = $repository;
        parent::__construct();
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.guide.datatable.action',
            'title' => 'admin.guide.datatable.title',
            'status' => 'admin.guide.datatable.status',
            'type' => 'admin.guide.datatable.type',
            'checkbox' => 'admin.common.checkbox',
        ];
    }

    /**
     * Get query source of dataTable.
     *
     * @return Builder
     */
    public function query(): Builder
    {
        return $this->repository->getByQueryBuilder([
            ['status', '!=', ActiveStatus::Deleted],
        ]);
    }

    public function setColumnSearch(): void
    {
        $this->columnAllSearch = [ 1, 2, 3, 4];
        $this->columnSearchDate = [4];
        $this->columnSearchSelect = [
            [
                'column' => 2,
                'data' => GuideType::asSelectArray()
            ],
            [
                'column' => 3,
                'data' => ActiveStatus::asSelectArray()
            ]
        ];
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.guide', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'created_at' => '{{ $created_at ? format_datetime($created_at) : "" }}',
            'action' => $this->view['action'],
            'title' => $this->view['title'],
            'status' => $this->view['status'],
            'type' => function ($guide) {
                return view($this->view['type'], [
                    'type' => $guide->type,
                ])->render();
            },
        ];
    }

    protected function setCustomAddColumns(): void
    {
        $this->customAddColumns = [
            'checkbox' => $this->view['checkbox'],
        ];
    }

    protected function setCustomRawColumns(): void
    {
        $this->customRawColumns = ['action', 'title', 'created_at', 'status', 'type', 'checkbox'];
    }

    public function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [
            'type' => function ($query, $keyword) {
                $query->where('type', 'like', '%' . $keyword . '%');
            },
        ];
    }
}
