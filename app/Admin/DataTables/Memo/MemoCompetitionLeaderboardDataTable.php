<?php

namespace App\Admin\DataTables\Memo;

use App\Admin\DataTables\BaseDataTable;
use App\Models\MemoCompetitionEntry;

class MemoCompetitionLeaderboardDataTable extends BaseDataTable
{
    protected $nameTable = 'memoLeaderboardTable';
    protected array $actions = ['reset', 'reload'];

    public function setView(): void
    {
        $this->view = [
            'ranking' => 'admin.memo-game.competition.leaderboard-datatable.ranking',
            'child_id' => 'admin.memo-game.competition.leaderboard-datatable.child',
            'parent_info' => 'admin.memo-game.competition.leaderboard-datatable.parent',
            'attempt_number' => 'admin.memo-game.competition.leaderboard-datatable.attempt',
            'rounds_progress' => 'admin.memo-game.competition.leaderboard-datatable.rounds',
            'total_time' => 'admin.memo-game.competition.leaderboard-datatable.time',
            'total_moves' => 'admin.memo-game.competition.leaderboard-datatable.moves',
            'status' => 'admin.memo-game.competition.leaderboard-datatable.status',
            'completed_at' => 'admin.memo-game.competition.leaderboard-datatable.completed_at',
            'action' => 'admin.memo-game.competition.leaderboard-datatable.action',
        ];
    }

    public function setColumnSearch(): void
    {
        // Cột 1 (Thí sinh), Cột 2 (Phụ huynh)
        $this->columnAllSearch = [1, 2];
    }

    public function query()
    {
        $competitionId = $this->competitionId 
            ?? ($this->competition?->id ?? request()->route('id') ?? request()->get('competition_id'));

        $query = MemoCompetitionEntry::query()
            ->with(['child.user', 'rounds.theme'])
            ->where('memo_competition_id', $competitionId);

        // Hỗ trợ lọc theo tab: ?filter_status=valid hoặc invalid
        if (request()->filled('filter_status')) {
            $filter = request()->get('filter_status');
            if ($filter === 'valid') {
                $query->where('is_valid', true);
            } elseif ($filter === 'invalid') {
                $query->where('is_valid', false);
            }
        }

        return $query
            ->orderBy('is_valid', 'desc')
            ->orderBy('total_time', 'asc')
            ->orderBy('total_moves', 'asc');
    }

    public function html()
    {
        $this->instanceHtml = $this->builder()
            ->setTableId($this->nameTable)
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(0, 'asc')
            ->selectStyleSingle();

        $this->htmlParameters();

        return $this->instanceHtml;
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.memo_competition_leaderboard', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'ranking' => function ($entry) {
                return view($this->view['ranking'], [
                    'entry' => $entry,
                    'ranking' => $entry->ranking,
                ])->render();
            },
            'child_id' => function ($entry) {
                return view($this->view['child_id'], [
                    'entry' => $entry,
                    'child' => $entry->child,
                ])->render();
            },
            'attempt_number' => function ($entry) {
                return view($this->view['attempt_number'], [
                    'entry' => $entry,
                    'attempt_number' => $entry->attempt_number,
                ])->render();
            },
            'total_time' => function ($entry) {
                return view($this->view['total_time'], [
                    'entry' => $entry,
                    'total_time' => $entry->total_time,
                ])->render();
            },
            'total_moves' => function ($entry) {
                return view($this->view['total_moves'], [
                    'entry' => $entry,
                    'total_moves' => $entry->total_moves,
                ])->render();
            },
            'status' => function ($entry) {
                return view($this->view['status'], [
                    'entry' => $entry,
                    'is_valid' => $entry->is_valid,
                    'status' => $entry->status,
                ])->render();
            },
            'completed_at' => function ($entry) {
                return view($this->view['completed_at'], [
                    'entry' => $entry,
                    'completed_at' => $entry->completed_at,
                    'started_at' => $entry->started_at,
                ])->render();
            },
        ];
    }

    protected function setCustomAddColumns(): void
    {
        $this->customAddColumns = [
            'parent_info' => function ($entry) {
                return view($this->view['parent_info'], [
                    'entry' => $entry,
                    'child' => $entry->child,
                ])->render();
            },
            'rounds_progress' => function ($entry) {
                return view($this->view['rounds_progress'], [
                    'entry' => $entry,
                    'rounds' => $entry->rounds,
                    'games_won' => $entry->games_won,
                ])->render();
            },
            'action' => function ($entry) {
                return view($this->view['action'], [
                    'entry' => $entry,
                    'child' => $entry->child,
                    'rounds' => $entry->rounds,
                    'ranking' => $entry->ranking,
                    'total_time' => $entry->total_time,
                    'total_moves' => $entry->total_moves,
                    'attempt_number' => $entry->attempt_number,
                ])->render();
            },
        ];
    }

    protected function setCustomRawColumns(): void
    {
        $this->customRawColumns = [
            'ranking',
            'child_id',
            'parent_info',
            'attempt_number',
            'rounds_progress',
            'total_time',
            'total_moves',
            'status',
            'completed_at',
            'action',
        ];
    }

    protected function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [
            'child_id' => function ($query, $keyword) {
                $query->whereHas('child', function ($q) use ($keyword) {
                    $q->where('fullname', 'like', "%{$keyword}%");
                });
            },
            'parent_info' => function ($query, $keyword) {
                $query->whereHas('child.user', function ($q) use ($keyword) {
                    $q->where('fullname', 'like', "%{$keyword}%")
                      ->orWhere('phone', 'like', "%{$keyword}%")
                      ->orWhere('email', 'like', "%{$keyword}%");
                });
            },
        ];
    }
}
