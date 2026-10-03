<?php

namespace App\Admin\Http\Controllers\GPA;

use App\Admin\DataTables\GPA\GPADataTable;
use App\Admin\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use App\Admin\Repositories\GPA\GPARepositoryInterface;
use App\Traits\ResponseController;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Enums\ActiveStatus;
use App\Models\ClassGrade;

class GPAController extends Controller
{
    use ResponseController;

    public function __construct(
        GPARepositoryInterface $repository
    ) {

        parent::__construct();

        $this->repository = $repository;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.gpa.index',
            'status' => 'admin.gpa.datatable.status',
            'name' => 'admin.gpa.datatable.name',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.gpa.index',
        ];
    }
    public function index(GPADataTable $dataTable)
    {
        $actionMultiple = $this->getActionMultiple();

        $baseQuery = ClassGrade::query()
            ->where('status', '!=', ActiveStatus::Deleted)
            ->where(function ($query) {
                $query->where('semester1_grade', '>', 0)
                    ->orWhere('semester2_grade', '>', 0)
                    ->orWhereHas('evaluations');
            });

        $totalRecords = (clone $baseQuery)->count();
        $completedRecords = (clone $baseQuery)
            ->whereHas('evaluations', fn ($query) => $query->where('semester', 'full_year'))
            ->count();
        $averageScore = (clone $baseQuery)->whereNotNull('full_year_grade')->avg('full_year_grade');
        $needsReview = (clone $baseQuery)
            ->whereHas('evaluations', fn ($query) => $query->whereIn('calculation_status', ['incomplete', 'invalid_input']))
            ->count();

        $reportCardStats = [
            'total' => $totalRecords,
            'completed' => $completedRecords,
            'completion_rate' => $totalRecords > 0 ? round(($completedRecords / $totalRecords) * 100) : 0,
            'average_score' => $averageScore !== null ? round((float) $averageScore, 1) : null,
            'needs_review' => $needsReview,
        ];

        return $dataTable->render(
            $this->view['index'],
            [
                'status' => ActiveStatus::asSelectArray(),
                'actionMultiple' => $actionMultiple,
                'reportCardStats' => $reportCardStats,
                'breadcrumbs' => $this->crums->add(__('Danh sách GPA')),
            ]
        );
    }

    protected function getActionMultiple(): array
    {
        return [
            'active' => ActiveStatus::Active->description(),
            'draft' => ActiveStatus::Draft->description(),
            'deleted' => ActiveStatus::Deleted->description()
        ];
    }
}
