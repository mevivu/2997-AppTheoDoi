<?php

namespace App\Admin\DataTables\AppFeedback;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Traits\Roles;
use App\Enums\Feedback\AppFeedbackFeature;
use App\Enums\Feedback\AppFeedbackStatus;
use App\Models\AppFeedback;
use Illuminate\Database\Eloquent\Builder;

class AppFeedbackDatatable extends BaseDataTable
{
    use Roles;

    protected $nameTable = 'AppFeedbackTable';

    public function setView(): void
    {
        $this->view = [
            'user' => 'admin.app_feedback.datatable.user',
            'status' => 'admin.app_feedback.datatable.status',
            'action' => 'admin.app_feedback.datatable.action',
        ];
    }

    public function setColumnSearch(): void
    {
        $this->columnAllSearch = [0, 1, 2, 3, 4, 5];
        $this->columnSearchDate = [4];
        $this->columnSearchSelect = [
            [
                'column' => 1,
                'data' => [
                    '5' => '5 sao',
                    '4' => '4 sao',
                    '3' => '3 sao',
                    '2' => '2 sao',
                    '1' => '1 sao',
                ],
            ],
            [
                'column' => 2,
                'data' => AppFeedbackFeature::asSelectArray(),
            ],
            [
                'column' => 5,
                'data' => AppFeedbackStatus::asSelectArray(),
            ],
        ];
    }

    public function query(): Builder
    {
        $query = AppFeedback::query()->with(['user', 'grantedPackage', 'grantedByAdmin']);

        $status = request('feedback_status') ?: request('status');
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $query->orderByRaw("
            CASE 
                WHEN status = '" . AppFeedbackStatus::PENDING->value . "' THEN 0 
                WHEN status = '" . AppFeedbackStatus::APPROVED->value . "' THEN 1 
                WHEN status = '" . AppFeedbackStatus::VIP_GRANTED->value . "' THEN 2 
                WHEN status = '" . AppFeedbackStatus::REJECTED->value . "' THEN 3 
                ELSE 4 
            END ASC
        ")->orderByDesc('created_at');

        return $query;
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.app_feedbacks', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'rating' => function (AppFeedback $feedback) {
                $rating = max(1, min(5, (int) $feedback->rating));
                $starsHtml = '';
                for ($i = 1; $i <= 5; $i++) {
                    $colorClass = $i <= $rating ? 'text-warning' : 'text-muted opacity-25';
                    $starsHtml .= '<i class="ti ti-star-filled fs-15 ' . $colorClass . '"></i>';
                }
                return '<div class="d-inline-flex flex-column align-items-center gap-1">'
                    . '<div class="d-flex align-items-center gap-0.5">' . $starsHtml . '</div>'
                    . '<span class="badge bg-warning-lt text-warning fw-bold fs-11">' . $rating . '/5 sao</span>'
                    . '</div>';
            },
            'favorite_feature' => function (AppFeedback $feedback) {
                return '<span class="badge bg-teal-lt text-teal fw-semibold px-2.5 py-1.5 fs-12 text-wrap text-start" style="line-height: 1.4;">'
                    . '<i class="ti ti-check me-1"></i>' . e($feedback->favorite_feature)
                    . '</span>';
            },
            'improvement_feedback' => function (AppFeedback $feedback) {
                return '<div class="text-dark fs-13" style="max-width: 340px; white-space: pre-wrap; word-break: break-word; line-height: 1.45;">'
                    . e($feedback->improvement_feedback)
                    . '</div>';
            },
            'created_at' => function (AppFeedback $feedback) {
                if (!$feedback->created_at) {
                    return '<span class="text-muted fs-12">-</span>';
                }
                return '<div class="text-center text-nowrap fs-12 text-muted" style="line-height: 1.4;">'
                    . '<i class="ti ti-clock me-1"></i>' . format_datetime($feedback->created_at)
                    . '</div>';
            },
            'status' => function (AppFeedback $feedback) {
                return view($this->view['status'], [
                    'feedback' => $feedback,
                ])->render();
            },
        ];
    }

    protected function setCustomAddColumns(): void
    {
        $this->customAddColumns = [
            'user' => function (AppFeedback $feedback) {
                return view($this->view['user'], [
                    'feedback' => $feedback,
                ])->render();
            },
            'action' => function (AppFeedback $feedback) {
                return view($this->view['action'], [
                    'feedback' => $feedback,
                ])->render();
            },
        ];
    }

    protected function setCustomRawColumns(): void
    {
        $this->customRawColumns = [
            'user',
            'rating',
            'favorite_feature',
            'improvement_feedback',
            'created_at',
            'status',
            'action',
        ];
    }

    public function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [
            'user' => function ($query, $keyword) {
                $keyword = trim($keyword);
                $cleanId = ltrim($keyword, '#');

                $query->where(function ($sub) use ($keyword, $cleanId) {
                    if (is_numeric($cleanId)) {
                        $sub->orWhere('user_id', (int) $cleanId);
                    }
                    $sub->orWhere('email', 'like', "%{$keyword}%")
                        ->orWhereHas('user', function ($uQuery) use ($keyword) {
                            $uQuery->where('fullname', 'like', "%{$keyword}%");
                        });
                });
            },
        ];
    }
}
