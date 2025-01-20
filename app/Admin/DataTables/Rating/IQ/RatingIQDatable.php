<?php

namespace App\Admin\DataTables\Rating\IQ;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Rating\RatingRepositoryInterface;
use App\Admin\Traits\Roles;
use App\Enums\Question\QuestionType;
use Illuminate\Database\Eloquent\Builder;

class RatingIQDatable extends BaseDataTable
{
    use Roles;

    protected $nameTable = 'ratingIQTable';

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

        $this->columnAllSearch = [1, 2, 3, 5, 6];
        $this->columnSearchDate = [5];
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
                'type' => QuestionType::IQ
            ]
        );
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.iq', []);
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
            'badge_image' => function ($rating) {
                return view($this->view['image'], [
                    'image' => $rating->badge_image,
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
            'badge_image'

        ];
    }
}
