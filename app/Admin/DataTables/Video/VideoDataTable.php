<?php

namespace App\Admin\DataTables\Video;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Video\VideoRepositoryInterface;
use App\Enums\ActiveStatus;
use App\Enums\Video\VideoAccessType;
class VideoDataTable extends BaseDataTable
{
    protected $nameTable = 'videoTable';

    public function __construct(VideoRepositoryInterface $repository)
    {
        $this->repository = $repository;
        parent::__construct();
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.videos.datatable.action',
            'thumbnail' => 'admin.videos.datatable.thumbnail',
            'access_type' => 'admin.videos.datatable.access_type',
            'is_preview' => 'admin.videos.datatable.is_preview',
            'status' => 'admin.videos.datatable.status',
            'checkbox' => 'admin.common.checkbox',
        ];
    }

    public function setColumnSearch(): void
    {
        $categoryOptions = [];

        $this->columnAllSearch = [2, 3, 4, 5, 6, 7, 8, 9];
        $this->columnSearchSelect = [
            [
                'column' => 3,
                'data' => $categoryOptions,
            ],
            [
                'column' => 4,
                'data' => VideoAccessType::asSelectArray(),
            ],
            [
                'column' => 5,
                'data' => [
                    '1' => 'Xem thử',
                    '0' => 'Không',
                ],
            ],
            [
                'column' => 9,
                'data' => ActiveStatus::asSelectArray(),
            ],
        ];
    }

    public function query()
    {
        return $this->repository->getQueryBuilder()
            ->orderBy('sort_order', 'asc');
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.video', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'thumbnail' => $this->view['thumbnail'],
            'category' => fn($row) => '—',
            'access_type' => $this->view['access_type'],
            'is_preview' => $this->view['is_preview'],
            'duration_seconds' => fn($row) => $row->duration_seconds ? gmdate('H:i:s', $row->duration_seconds) : '—',
            'status' => $this->view['status'],
        ];
    }

    protected function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [
            'is_preview' => function ($query, $keyword) {
                $query->where('is_preview', (bool) $keyword);
            },
        ];
    }

    protected function setCustomAddColumns(): void
    {
        $this->customAddColumns = [
            'action' => $this->view['action'],
            'checkbox' => $this->view['checkbox'],
        ];
    }

    protected function setCustomRawColumns(): void
    {
        $this->customRawColumns = ['thumbnail', 'access_type', 'is_preview', 'action', 'status', 'checkbox'];
    }
}
