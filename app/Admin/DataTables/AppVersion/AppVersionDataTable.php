<?php

namespace App\Admin\DataTables\AppVersion;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\AppVersion\AppVersionRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class AppVersionDataTable extends BaseDataTable
{
    protected array $actions = ['reset', 'reload'];

    protected $nameTable = 'appVersionTable';

    public function __construct(
        AppVersionRepositoryInterface $repository
    ) {
        $this->repository = $repository;
        parent::__construct();
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.app_versions.datatable.action',
            'status' => 'admin.app_versions.datatable.status',
            'platform' => 'admin.app_versions.datatable.platform',
            'app_type' => 'admin.app_versions.datatable.app_type',
            'checkbox' => 'admin.common.checkbox',
        ];
    }

    public function setColumnSearch(): void
    {
        $this->columnAllSearch = [1, 2, 3, 4, 5, 6, 7];
        $this->columnSearchSelect = [1, 2, 7];
    }

    public function query(): Builder
    {
        return $this->repository->getQueryBuilder();
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.app_versions', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'checkbox' => $this->view['checkbox'],
            'app_type' => function ($row) {
                return view($this->view['app_type'], ['row' => $row])->render();
            },
            'platform' => function ($row) {
                return view($this->view['platform'], ['row' => $row])->render();
            },
            'status' => function ($row) {
                return view($this->view['status'], ['row' => $row])->render();
            },
            'update_url' => function ($row) {
                return '<a href="' . e($row->update_url) . '" target="_blank" class="text-truncate d-inline-block" style="max-width: 200px;">' . e($row->update_url) . '</a>';
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
        $this->customRawColumns = ['checkbox', 'app_type', 'platform', 'status', 'update_url', 'action'];
    }
}
