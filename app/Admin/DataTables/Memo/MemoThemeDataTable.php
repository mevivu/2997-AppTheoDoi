<?php

namespace App\Admin\DataTables\Memo;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\MemoTheme\MemoThemeRepositoryInterface;
use App\Enums\ActiveStatus;

class MemoThemeDataTable extends BaseDataTable
{
    protected $nameTable = 'memoThemeTable';

    public function __construct(MemoThemeRepositoryInterface $repository)
    {
        $this->repository = $repository;
        parent::__construct();
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.memo-game.theme.datatable.action',
            'status' => 'admin.memo-game.theme.datatable.status',
            'icon' => 'admin.memo-game.theme.datatable.icon',
            'card_back_type' => 'admin.memo-game.theme.datatable.card-back-type',
            'age' => 'admin.memo-game.theme.datatable.age',
            'checkbox' => 'admin.common.checkbox',
            'cards_count' => 'admin.memo-game.theme.datatable.cards-count',
        ];
    }

    public function setColumnSearch(): void
    {
        $this->columnAllSearch = [2, 3];
        $this->columnSearchSelect = [
            [
                'column' => 4,
                'data' => [
                    'theme' => 'Ảnh chủ đề',
                    'logo' => 'Ảnh logo',
                ],
            ],
            [
                'column' => 8,
                'data' => ActiveStatus::asSelectArray(),
            ],
        ];
    }

    public function query()
    {
        return $this->repository->getQueryBuilder()
            ->withCount('cards')
            ->where('status', '!=', ActiveStatus::Deleted->value)
            ->orderBy('position', 'asc');
    }

    public function html()
    {
        $this->instanceHtml = $this->builder()
            ->setTableId($this->nameTable)
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(7, 'asc')
            ->selectStyleSingle();

        $this->htmlParameters();

        return $this->instanceHtml;
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.memo_themes', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'icon' => $this->view['icon'],
            'card_back_type' => $this->view['card_back_type'],
            'age' => $this->view['age'],
            'status' => $this->view['status'],
            'cards_count' => $this->view['cards_count'],
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
        $this->customRawColumns = ['checkbox', 'icon', 'card_back_type', 'age', 'status', 'cards_count', 'action'];
    }
}
