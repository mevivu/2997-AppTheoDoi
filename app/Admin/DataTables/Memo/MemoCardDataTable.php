<?php

namespace App\Admin\DataTables\Memo;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\MemoCard\MemoCardRepositoryInterface;
use App\Admin\Repositories\MemoTheme\MemoThemeRepositoryInterface;
use App\Enums\ActiveStatus;

class MemoCardDataTable extends BaseDataTable
{
    protected $nameTable = 'memoCardTable';
    protected $themeRepository;

    public function __construct(
        MemoCardRepositoryInterface $repository,
        MemoThemeRepositoryInterface $themeRepository
    ) {
        $this->repository = $repository;
        $this->themeRepository = $themeRepository;
        parent::__construct();
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.memo-game.card.datatable.action',
            'status' => 'admin.memo-game.card.datatable.status',
            'image' => 'admin.memo-game.card.datatable.image',
            'audio' => 'admin.memo-game.card.datatable.audio',
            'theme_id' => 'admin.memo-game.card.datatable.theme',
            'checkbox' => 'admin.common.checkbox',
        ];
    }

    public function setColumnSearch(): void
    {
        $themes = $this->themeRepository->getActiveThemes()->pluck('name', 'id')->toArray();

        $this->columnAllSearch = [2, 3, 5];
        $this->columnSearchSelect = [
            [
                'column' => 3,
                'data' => $themes,
            ],
            [
                'column' => 5,
                'data' => ActiveStatus::asSelectArray(),
            ],
        ];
    }

    public function query()
    {
        $query = $this->repository->getQueryBuilder()
            ->with('theme')
            ->where('status', '!=', ActiveStatus::Deleted->value);

        if (request()->filled('theme_id')) {
            $query->where('memo_theme_id', request('theme_id'));
        }

        return $query->orderBy('id', 'desc');
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

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.memo_cards', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'image' => $this->view['image'],
            'theme_id' => $this->view['theme_id'],
            'audio' => $this->view['audio'],
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
        $this->customRawColumns = ['checkbox', 'image', 'theme_id', 'audio', 'status', 'action'];
    }
}
