<?php

namespace App\Admin\DataTables\Memo;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\MemoAgeConfig\MemoAgeConfigRepositoryInterface;
use App\Enums\ActiveStatus;

class MemoAgeConfigDataTable extends BaseDataTable
{
    protected $nameTable = 'memoAgeConfigTable';

    public function __construct(MemoAgeConfigRepositoryInterface $repository)
    {
        $this->repository = $repository;
        parent::__construct();
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.memo-game.config.datatable.action',
            'status' => 'admin.memo-game.config.datatable.status',
            'age_range' => 'admin.memo-game.config.datatable.age-range',
            'grid_size' => 'admin.memo-game.config.datatable.grid-size',
            'total_cards' => 'admin.memo-game.config.datatable.total-cards',
            'total_duration' => 'admin.memo-game.config.datatable.duration',
            'total_rounds' => 'admin.memo-game.config.datatable.rounds',
            'max_mistakes' => 'admin.memo-game.config.datatable.max-mistakes',
            'checkbox' => 'admin.common.checkbox',
        ];
    }

    public function setColumnSearch(): void
    {
        $this->columnAllSearch = [1, 8];
        $this->columnSearchSelect = [
            [
                'column' => 8,
                'data' => ActiveStatus::asSelectArray(),
            ],
        ];
    }

    public function query()
    {
        return $this->repository->getQueryBuilder()
            ->where('status', '!=', ActiveStatus::Deleted->value)
            ->orderBy('min_age', 'asc');
    }

    public function html()
    {
        $this->instanceHtml = $this->builder()
            ->setTableId($this->nameTable)
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(2, 'asc')
            ->selectStyleSingle();

        $this->htmlParameters();

        return $this->instanceHtml;
    }

    protected function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [
            'name' => function ($query, $keyword) {
                $query->where('name', 'like', "%{$keyword}%");
            },
            'status' => function ($query, $keyword) {
                $query->where('status', $keyword);
            },
        ];
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.memo_age_configs', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'age_range' => $this->view['age_range'],
            'grid_size' => $this->view['grid_size'],
            'total_cards' => $this->view['total_cards'],
            'total_duration' => $this->view['total_duration'],
            'total_rounds' => $this->view['total_rounds'],
            'max_mistakes' => $this->view['max_mistakes'],
            'status' => $this->view['status'],
            'action' => $this->view['action'],
        ];
    }

    protected function setCustomAddColumns(): void
    {
        $this->customAddColumns = [
            'checkbox' => $this->view['checkbox'],
            'action' => $this->view['action'],
        ];
    }

    protected function setCustomRawColumns(): void
    {
        $this->customRawColumns = ['checkbox', 'age_range', 'grid_size', 'total_cards', 'total_duration', 'total_rounds', 'max_mistakes', 'status', 'action'];
    }
}
