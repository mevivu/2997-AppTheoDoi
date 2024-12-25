<?php

namespace App\Admin\DataTables\Subject;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Subject\SubjectRepositoryInterface;
use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Builder;

class SubjectDataTable extends BaseDataTable
{
    protected $nameTable = 'subjectTable';

    protected $classRepository;

    public function __construct(
        SubjectRepositoryInterface $repository
    ) {
        $this->repository = $repository;

        parent::__construct();

    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.subject.datatable.action',
            'status' => 'admin.subject.datatable.status',
            'name' => 'admin.subject.datatable.name',
            'checkbox' => 'admin.common.checkbox',
        ];
    }


    public function setColumnSearch(): void
    {
        $this->columnAllSearch = [1, 2, 3];
        $this->columnSearchDate = [3];
        $this->columnSearchSelect = [

            [
                'column' => 2,
                'data' => ActiveStatus::asSelectArray()
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
        return $this->repository->getByQueryBuilder(
            [
                ['status', '!=', ActiveStatus::Deleted],
            ]
        );
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.subjects', []);
    }

    public function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [
            'class_id' => function ($query, $value) {
                $query->where('class_id', $value);
            },
        ];
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'status' => $this->view['status'],
            'checkbox' => $this->view['checkbox'],
            'name' => $this->view['name'],
            'created_at' => function ($query) {
                return format_datetime($query->created_at);
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
            'name',
            'status',
            'action',
            'checkbox',
        ];
    }

}