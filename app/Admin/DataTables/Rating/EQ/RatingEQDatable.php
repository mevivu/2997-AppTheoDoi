<?php

namespace App\Admin\DataTables\Rating\EQ;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Rating\RatingRepositoryInterface;
use App\Admin\Traits\Roles;
use App\Enums\Question\QuestionType;
use Illuminate\Database\Eloquent\Builder;

class RatingEQDatable extends BaseDataTable
{
    use Roles;

    protected $nameTable = 'ratingEQTable';

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

        $this->columnAllSearch = [1, 2, 3, 4, 5, 6, 7, 8, 9];
        $this->columnSearchDate = [9];
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
                'type' => QuestionType::EQ
            ],
            ['child.user']
        );
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.eq', []);
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
            'parent_code' => function ($row) {
                if ($row->child && $row->child->user) {
                    return view('admin.users.datatable.editlink', [
                        'id' => $row->child->user->id,
                        'code' => 'CM' . $row->child->user->id
                    ])->render();
                }
                return '';
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
            'parent_code',
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
                case 'parent_code':
                    return ($row->child && $row->child->user) ? 'CM' . $row->child->user->id : '';
            }
        } catch (\Throwable $e) {
            return '';
        }

        return parent::getExportValue($key, $row);
    }
}
