<?php

namespace App\Admin\DataTables\IqNorm;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\IqNorm\IqNormRepositoryInterface;
use App\Enums\ActiveStatus;

class IqNormDataTable extends BaseDataTable
{
    protected $nameTable = 'iqNormTable';

    public function __construct(
        IqNormRepositoryInterface $repository
    ) {
        $this->repository = $repository;

        parent::__construct();
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.iq_norms.datatable.action',
            'status' => 'admin.iq_norms.datatable.status',
            'checkbox' => 'admin.common.checkbox',
        ];
    }

    public function setColumnSearch(): void
    {
        $this->columnAllSearch = [1, 2, 3, 4, 7];

        $this->columnSearchSelect = [
            [
                'column' => 7,
                'data' => ActiveStatus::asSelectArray(),
            ],
        ];
    }

    public function query()
    {
        return $this->repository->getQueryBuilder()->orderBy('age', 'asc');
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.iq_norms', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'checkbox' => $this->view['checkbox'],
            'age' => function ($row) {
                return '<span class="badge bg-purple-lt fs-13 fw-bold px-2 py-1"><i class="ti ti-timeline me-1"></i>' . $row->age . ' tuổi</span>';
            },
            'mean_score' => function ($row) {
                return '<span class="fw-bold text-dark fs-13">' . number_format($row->mean_score, 2) . '</span>';
            },
            'sd_score' => function ($row) {
                return '<span class="fw-bold text-azure fs-13">±' . number_format($row->sd_score, 2) . '</span>';
            },
            'sample_size' => function ($row) {
                return '<span class="badge bg-blue-lt text-blue px-2 py-0.5"><i class="ti ti-users me-1"></i>N = ' . number_format($row->sample_size) . '</span>';
            },
            'score_range' => function ($row) {
                $min = $row->min_raw_score !== null ? (float) $row->min_raw_score : 20;
                $max = $row->max_raw_score !== null ? (float) $row->max_raw_score : 150;
                return '<span class="text-muted fs-12">' . $min . ' – ' . $max . ' điểm</span>';
            },
            'version' => function ($row) {
                return '<span class="badge bg-secondary-lt text-muted fs-11">' . e($row->version) . '</span>';
            },
            'status' => $this->view['status'],
            'note' => function ($row) {
                return '<span class="text-muted fs-12 text-truncate d-inline-block" style="max-width: 200px;" title="' . e($row->note) . '">' . e($row->note ?: '-') . '</span>';
            },
        ];
    }

    protected function setCustomAddColumns(): void
    {
        $this->customAddColumns = [
            'action' => $this->view['action'],
        ];
    }

    protected function setCustomRawColumns(): void
    {
        $this->customRawColumns = ['checkbox', 'age', 'mean_score', 'sd_score', 'sample_size', 'score_range', 'version', 'status', 'note', 'action'];
    }
}
