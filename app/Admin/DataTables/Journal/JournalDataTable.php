<?php

namespace App\Admin\DataTables\Journal;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Children\ChildrenRepositoryInterface;
use App\Admin\Repositories\Journal\JournalRepositoryInterface;
use App\Admin\Traits\Roles;
use App\Enums\Child\ChildStatus;
use App\Enums\Journal\JournalType;
use App\Enums\User\Gender;
use Illuminate\Database\Eloquent\Builder;

class JournalDataTable extends BaseDataTable
{
    protected $nameTable = 'JournalTable';


    public function __construct(
        JournalRepositoryInterface $repository
    ) {
        $this->repository = $repository;

        parent::__construct();

    }

    public function setView(): void
    {
        $this->view = [
            'title'=>'admin.journals.datatable.title',
            'child'=>'admin.journals.datatable.child',
            'type'=>'admin.journals.datatable.type',
            'action' => 'admin.journals.datatable.action',

        ];
    }


    public function setColumnSearch(): void
    {
        $this->columnAllSearch = [0, 1];

        $this->columnSearchDate = [3];

        $this->columnSearchSelect = [
            [
                'column' => 4,
                'data' => Gender::asSelectArray()
            ],
            [
                'column' => 5,
                'data' => ChildStatus::asSelectArray()
            ],
        ];
    }


    /**
     * Get query source of dataTable.
     *
     * @return Builder
     */
    public function query(): Builder
    {
        return $this->repository->getQueryBuilderOrderBy();
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.journal', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'type' => $this->view['type'],
            'title' => $this->view['title'],
            'child_id' => function ($children) {
                return view($this->view['child'], [
                    'child' => $children->child,
                ])->render();
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
            'title',
            'child_id',
            'type',
            'action',


        ];
    }


}
