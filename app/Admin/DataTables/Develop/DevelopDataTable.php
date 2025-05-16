<?php

namespace App\Admin\DataTables\Develop;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Guide\GuideRepositoryInterface;
use App\Enums\ActiveStatus;
use App\Enums\Guide\GuideType;
use Illuminate\Database\Eloquent\Builder;

class DevelopDataTable extends BaseDataTable
{
    protected $nameTable = 'developTable';

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
            'action' => 'admin.develop.datatable.action',
            'title' => 'admin.develop.datatable.title',
            'status' => 'admin.develop.datatable.status',
            'type' => 'admin.develop.datatable.type',
            'checkbox' => 'admin.common.checkbox',
            'steps' => 'admin.develop.datatable.steps',
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
            ['type', '=', GuideType::Develop],
        ]);
    }

    public function setColumnSearch(): void
    {
        $this->columnAllSearch = [1, 2, 3];
        $this->columnSearchDate = [3];
        $this->columnSearchSelect = [

            [
                'column' => 2,
                'data' => ActiveStatus::asSelectArray()
            ]
        ];
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.develop', []);
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
            'steps' => $this->view['steps']
        ];
    }

    protected function setCustomRawColumns(): void
    {
        $this->customRawColumns = ['action', 'title', 'created_at', 'status', 'type', 'checkbox', 'steps'];
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
