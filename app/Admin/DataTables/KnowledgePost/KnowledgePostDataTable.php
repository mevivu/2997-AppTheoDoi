<?php

namespace App\Admin\DataTables\KnowledgePost;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Post\PostRepositoryInterface;
use App\Admin\Traits\GetConfig;
use App\Enums\FeaturedStatus;
use App\Enums\Post\PostStatus;
use App\Enums\Post\PostType;

class KnowledgePostDataTable extends BaseDataTable
{
    use GetConfig;

    protected $nameTable = 'KnowledgePostTable';
    protected array $actions = ['reset', 'reload'];

    public function __construct(
        PostRepositoryInterface $repository
    ) {
        parent::__construct();

        $this->repository = $repository;
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.knowledge_posts.datatable.action',
            'image' => 'admin.posts.datatable.image',
            'editlink' => 'admin.knowledge_posts.datatable.editlink',
            'status' => 'admin.posts.datatable.status',
            'is_featured' => 'admin.posts.datatable.is-featured',
            'checkbox' => 'admin.common.checkbox',
        ];
    }

    public function setColumnSearch(): void
    {
        $this->columnAllSearch = [2, 3, 4, 5];
        $this->columnSearchDate = [5];
        $this->columnSearchSelect = [
            [
                'column' => 3,
                'data' => PostStatus::asSelectArray()
            ],
            [
                'column' => 4,
                'data' => FeaturedStatus::asSelectArray()
            ],
        ];
    }

    public function query()
    {
        return $this->repository->getQueryBuilderOrderBy('id', 'DESC')->where('type', PostType::Knowledge);
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.knowledge_post', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'image' => $this->view['image'],
            'status' => $this->view['status'],
            'title' => $this->view['editlink'],
            'is_featured' => $this->view['is_featured'],
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
        $this->customRawColumns = ['image', 'title', 'status', 'is_featured', 'action', 'checkbox'];
    }
}
