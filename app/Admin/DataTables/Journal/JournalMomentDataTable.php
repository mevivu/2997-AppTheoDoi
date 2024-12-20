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

class JournalMomentDataTable extends BaseDataTable
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
            'action' => 'admin.journals.datatable.action',

        ];
    }


    public function setColumnSearch(): void
    {
        $this->columnAllSearch = [0, 1,2];

        $this->columnSearchDate = [2];


    }


    /**
     * Get query source of dataTable.
     *
     * @return Builder
     */
    public function query(): Builder
    {
        return $this->repository->getByQueryBuilder(['type'=>JournalType::Moment]);
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.journal', []);
    }
    public function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [
            'child_id' => function ($query, $keyword) {
                $query->whereHas('child', function ($subQuery) use ($keyword) {
                    $subQuery->where('fullname', 'like', "%$keyword%");
                });
            },
            'title' => function ($query, $keyword) {
                $query->where('title', 'like', "%$keyword%");
            },

        ];
    }
    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [

            'title' => $this->view['title'],
            'child_id' => function ($children) {
                return view($this->view['child'], [
                    'child' => $children->child,
                ])->render();
            },
            'created_at' => '{{ date("d-m-Y", strtotime($created_at)) }}',

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
