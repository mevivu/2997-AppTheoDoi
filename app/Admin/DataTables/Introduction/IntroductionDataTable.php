<?php

namespace App\Admin\DataTables\Introduction;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Introduction\IntroductionRepositoryInterface;
use App\Admin\Traits\GetConfig;
use App\Enums\DefaultStatus;
use App\Enums\Introduction\IntroductionSectionType;

class IntroductionDataTable extends BaseDataTable
{
    use GetConfig;

    protected $nameTable = 'IntroductionTable';
    protected array $actions = ['reset', 'reload'];

    public function __construct(IntroductionRepositoryInterface $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.introductions.datatable.action',
            'image' => 'admin.introductions.datatable.image',
            'editlink' => 'admin.introductions.datatable.editlink',
            'section_type' => 'admin.introductions.datatable.section-type',
            'status' => 'admin.introductions.datatable.status',
            'checkbox' => 'admin.common.checkbox',
        ];
    }

    public function setColumnSearch(): void
    {
        $this->columnAllSearch = [2, 3, 4, 5];
        $this->columnSearchSelect = [
            [
                'column' => 3,
                'data' => IntroductionSectionType::asSelectArray(),
            ],
            [
                'column' => 5,
                'data' => [
                    DefaultStatus::Published->value => 'Xuất bản',
                    DefaultStatus::Draft->value => 'Bản nháp / Ẩn',
                ],
            ],
        ];
    }

    public function query()
    {
        return $this->repository->getQueryBuilderOrderBy('sort_order', 'asc');
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.introduction', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'image' => $this->view['image'],
            'title' => $this->view['editlink'],
            'section_type' => $this->view['section_type'],
            'status' => $this->view['status'],
            'created_at' => '{{ date("d-m-Y", strtotime($created_at)) }}',
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
        $this->customRawColumns = ['image', 'title', 'section_type', 'status', 'action', 'checkbox'];
    }
}
