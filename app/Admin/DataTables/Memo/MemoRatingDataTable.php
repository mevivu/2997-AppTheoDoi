<?php

namespace App\Admin\DataTables\Memo;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\MemoRating\MemoRatingRepositoryInterface;

class MemoRatingDataTable extends BaseDataTable
{
    protected $nameTable = 'memoRatingTable';

    public function __construct(MemoRatingRepositoryInterface $repository)
    {
        $this->repository = $repository;
        parent::__construct();
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.memo-game.rating.datatable.action',
            'child_id' => 'admin.memo-game.rating.datatable.child',
            'theme_id' => 'admin.memo-game.rating.datatable.theme',
            'score' => 'admin.memo-game.rating.datatable.score',
            'evaluation_label' => 'admin.memo-game.rating.datatable.evaluation',
            'total_duration_spent' => 'admin.memo-game.rating.datatable.duration',
            'checkbox' => 'admin.common.checkbox',
        ];
    }

    public function setColumnSearch(): void
    {
        $this->columnAllSearch = [1, 9];
        $this->columnSearchDate = [9];
    }

    public function query()
    {
        return $this->repository->getQueryBuilder()
            ->with(['child', 'theme', 'ageConfig', 'rounds'])
            ->orderBy('id', 'desc');
    }

    public function html()
    {
        $this->instanceHtml = $this->builder()
            ->setTableId($this->nameTable)
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(9, 'desc')
            ->selectStyleSingle();

        $this->htmlParameters();

        return $this->instanceHtml;
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.memo_ratings', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'child_id' => $this->view['child_id'],
            'theme_id' => $this->view['theme_id'],
            'total_duration_spent' => $this->view['total_duration_spent'],
            'score' => $this->view['score'],
            'evaluation_label' => $this->view['evaluation_label'],
            'created_at' => '{{ format_date($created_at) }}',
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
        $this->customRawColumns = ['checkbox', 'child_id', 'theme_id', 'total_duration_spent', 'score', 'evaluation_label', 'action'];
    }
}
