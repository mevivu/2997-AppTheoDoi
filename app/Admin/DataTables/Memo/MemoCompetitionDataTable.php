<?php

namespace App\Admin\DataTables\Memo;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\MemoCompetition\MemoCompetitionRepositoryInterface;

class MemoCompetitionDataTable extends BaseDataTable
{
    protected $nameTable = 'memoCompetitionTable';

    public function __construct(MemoCompetitionRepositoryInterface $repository)
    {
        $this->repository = $repository;
        parent::__construct();
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.memo-game.competition.datatable.action',
            'status' => 'admin.memo-game.competition.datatable.status',
            'banner_image' => 'admin.memo-game.competition.datatable.banner',
            'name' => 'admin.memo-game.competition.datatable.name',
            'timeline' => 'admin.memo-game.competition.datatable.timeline',
            'themes' => 'admin.memo-game.competition.datatable.themes',
            'stats' => 'admin.memo-game.competition.datatable.stats',
            'checkbox' => 'admin.common.checkbox',
        ];
    }

    public function setColumnSearch(): void
    {
        $this->columnAllSearch = [2];
        $this->columnSearchSelect = [
            [
                'column' => 6,
                'data' => [
                    'active' => 'Đang mở (Active)',
                    'upcoming' => 'Sắp diễn ra (Upcoming)',
                    'draft' => 'Bản nháp (Draft)',
                    'ended' => 'Đã kết thúc (Ended)',
                    'cancelled' => 'Đã hủy (Cancelled)',
                ],
            ],
        ];
    }

    public function query()
    {
        return $this->repository->getQueryBuilder()
            ->with(['ageConfig', 'competitionThemes.theme'])
            ->withCount([
                'entries as total_entries',
                'entries as valid_entries' => function ($q) {
                    $q->where('is_valid', true);
                }
            ])
            ->orderBy('id', 'desc');
    }

    public function html()
    {
        $this->instanceHtml = $this->builder()
            ->setTableId($this->nameTable)
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(2, 'desc')
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
        $this->customColumns = config('datatables_columns.memo_competitions', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'banner_image' => $this->view['banner_image'],
            'name' => $this->view['name'],
            'timeline' => $this->view['timeline'],
            'themes' => $this->view['themes'],
            'stats' => $this->view['stats'],
            'status' => $this->view['status'],
            'action' => $this->view['action'],
        ];
    }

    protected function setCustomAddColumns(): void
    {
        $this->customAddColumns = [
            'checkbox' => $this->view['checkbox'],
            'timeline' => $this->view['timeline'],
            'themes' => $this->view['themes'],
            'stats' => $this->view['stats'],
            'action' => $this->view['action'],
        ];
    }

    protected function setCustomRawColumns(): void
    {
        $this->customRawColumns = ['checkbox', 'banner_image', 'name', 'timeline', 'themes', 'stats', 'status', 'action'];
    }
}
