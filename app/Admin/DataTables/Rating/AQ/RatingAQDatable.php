<?php

namespace App\Admin\DataTables\Rating\AQ;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Rating\RatingRepositoryInterface;
use App\Admin\Traits\Roles;
use App\Enums\Question\QuestionType;
use Illuminate\Database\Eloquent\Builder;

class RatingAQDatable extends BaseDataTable
{
    use Roles;

    protected $nameTable = 'ratingAQTable';

    protected array $actions = ['reset', 'reload', 'excel'];

    public function __construct(
        RatingRepositoryInterface $repository
    )
    {
        $this->repository = $repository;

        parent::__construct();
    }

    public function setView(): void
    {
        $this->view = [
            'checkbox' => 'admin.common.checkbox',
            'action' => 'admin.rating.datatable.action',
            'name' => 'admin.rating.datatable.name',
            'image' => 'admin.rating.datatable.image',
        ];
    }

    public function setColumnSearch(): void
    {

        $this->columnAllSearch = [1, 2, 3, 4];
        $this->columnSearchDate = [4];
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
        return $this->repository->getByQueryBuilder(
            [
                'type' => QuestionType::AQ
            ]
        );
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.aq', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'checkbox' => $this->view['checkbox'],
            'created_at' => '{{ format_datetime($created_at) }}',

            'child_id' => function ($rating) {
                return view($this->view['name'], [
                    'child' => $rating->child,
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

    protected function setCustomRawColumns(): void
    {
        $this->customRawColumns = [
            'child_id',
            'action',
            'checkbox',

        ];
    }
}
