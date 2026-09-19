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

        $this->columnAllSearch = [1, 2, 3, 5, 6];
        $this->columnSearchDate = [6];
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
        $query = $this->repository->getByQueryBuilder(
            [
                'type' => QuestionType::IQ
            ],
            ['child.user', 'memoTheme', 'memoAgeConfig']
        )->whereNotNull('score')->where('score', '>', 0);

        if (request()->filled('child_id')) {
            $query->where('child_id', request('child_id'));
        }

        return $query->orderBy('id', 'desc');
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.iq', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'checkbox' => $this->view['checkbox'],
            'updated_at' => '{{ format_datetime($updated_at) }}',
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
            'badge_image' => function ($rating) {
                return view($this->view['image'], [
                    'image' => $rating->badge_image,
                ])->render();
            },
            'linguistic' => function ($rating) {
                return $rating->linguistic ? '<span class="badge bg-indigo-lt fs-13 fw-bold">' . e($rating->linguistic) . '</span>' : '-';
            },
            'logic_math' => function ($rating) {
                return $rating->logic_math ? '<span class="badge bg-blue-lt fs-13 fw-bold">' . e($rating->logic_math) . '</span>' : '-';
            },
            'visual' => function ($rating) {
                return $rating->visual ? '<span class="badge bg-azure-lt fs-13 fw-bold">' . e($rating->visual) . '</span>' : '-';
            },
            'memory' => function ($rating) {
                return $rating->memory ? '<span class="badge bg-teal-lt text-teal fs-13 fw-bold">' . e($rating->memory) . '</span>' : '-';
            },
            'game_info' => function ($rating) {
                if ($rating->version !== 'v2' && $rating->game_score === null && $rating->memo_theme_id === null) {
                    return '<span class="badge bg-secondary-lt text-muted fs-11" title="Bài test IQ V1 không có phần chơi game">-</span>';
                }

                $score = $rating->game_score ?? 0;
                $maxRounds = $rating->memoAgeConfig?->total_rounds ?? 3;
                $duration = (int) ($rating->game_duration_spent ?? 0);
                $durationMin = floor($duration / 60);
                $durationSec = $duration % 60;
                $durationStr = $durationMin > 0 ? "{$durationMin}p{$durationSec}s" : "{$durationSec}s";

                $html = '<div class="d-inline-flex flex-column align-items-center gap-1 py-1">';
                $html .= '<span class="badge bg-blue-lt text-blue fs-12 fw-bold px-2 py-0.5 rounded-pill"><i class="ti ti-trophy me-1"></i>' . e($score) . '/' . e($maxRounds) . ' ván</span>';

                $metaItems = [];
                if ($duration > 0) {
                    $metaItems[] = '<span title="Thời gian làm bài game"><i class="ti ti-clock fs-11 text-muted"></i> ' . e($durationStr) . '</span>';
                }
                if ($rating->game_pairs_matched !== null) {
                    $metaItems[] = '<span title="Số cặp hình ghép đúng"><i class="ti ti-cards fs-11 text-success"></i> ' . e($rating->game_pairs_matched) . '</span>';
                }
                if ($rating->game_mistakes !== null) {
                    $metaItems[] = '<span title="Số lần lật sai" class="text-danger"><i class="ti ti-x fs-11"></i> ' . e($rating->game_mistakes) . '</span>';
                }

                if (!empty($metaItems)) {
                    $html .= '<div class="fs-11 text-muted d-flex align-items-center gap-1.5">' . implode(' • ', $metaItems) . '</div>';
                }

                if ($rating->memoTheme) {
                    $html .= '<span class="badge bg-azure-lt text-azure fs-10 px-1.5 py-0 rounded" title="Chủ đề lật thẻ"><i class="ti ti-palette me-0.5"></i>' . e($rating->memoTheme->name) . '</span>';
                }

                $html .= '</div>';
                return $html;
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
            'badge_image',
            'linguistic',
            'logic_math',
            'visual',
            'memory',
            'game_info',
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
                case 'game_info':
                    return ($row->game_score !== null) ? "{$row->game_score} ván thắng ({$row->game_duration_spent}s)" : '';
            }
        } catch (\Throwable $e) {
            return '';
        }

        return parent::getExportValue($key, $row);
    }
}
