<?php

namespace App\Admin\DataTables\RatingPQ;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\RatingPQ\RatingPQRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class RatingPQDataTable extends BaseDataTable
{
    protected $nameTable = 'ratingPQTable';

    protected array $actions = [ 'reset', 'reload', 'excel'];


    public function __construct(
        RatingPQRepositoryInterface $repository
    )
    {
        $this->repository = $repository;

        parent::__construct();

    }

    public function setView(): void
    {
        $this->view = [
            'child' => 'admin.ratingPQ.datatable.child',
            'action' => 'admin.ratingPQ.datatable.action',
            'checkbox' => 'admin.common.checkbox',
        ];
    }


    public function setColumnSearch(): void
    {
        $this->columnAllSearch = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

        $this->columnSearchDate = [2];

        $this->columnSearchSelect = [

        ];
    }


    /**
     * Get query source of dataTable.
     *
     * @return Builder
     */
    public function query(): Builder
    {
        return $this->repository->getQueryBuilder()->with(['child'])->orderBy('id', 'desc');
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.rating_pqs', []);
    }

    public function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [
            'child_id' => function ($query, $keyword) {
                $query->whereHas('child', function ($subQuery) use ($keyword) {
                    $subQuery->where('fullname', 'like', "%$keyword%");
                });
            },

        ];
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'checkbox' => $this->view['checkbox'],
            'child_id' => function ($children) {
                return view($this->view['child'], [
                    'child' => $children->child,
                ])->render();
            },
            'assessment_date' => '{{ format_date($assessment_date) }}',

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
            'child_id',
            'action',
            'checkbox',

        ];
    }

    protected function getExportValue($key, $row)
    {
        try {
            switch ($key) {
                case 'child_id':
                    return $row->child_id ? 'TE' . $row->child_id : '';
            }
        } catch (\Throwable $e) {
            return '';
        }

        return parent::getExportValue($key, $row);
    }
}
