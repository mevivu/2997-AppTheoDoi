<?php

namespace App\Admin\Http\Controllers\Journal;

use App\Admin\DataTables\Journal\JournalMilestoneDataTable;
use App\Admin\DataTables\Journal\UserJournalMilestoneDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Journal\JournalMilestoneRequest;
use App\Admin\Repositories\Journal\JournalMilestoneRepositoryInterface;
use App\Admin\Services\Journal\JournalMilestoneServiceInterface;
use App\Enums\Journal\MilestoneStage;
use App\Models\JournalMilestone;
use App\Traits\ResponseController;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class JournalMilestoneController extends Controller
{
    use ResponseController;

    public function __construct(
        JournalMilestoneRepositoryInterface $repository,
        JournalMilestoneServiceInterface    $service
    ) {
        parent::__construct();
        $this->repository = $repository;
        $this->service = $service;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.journal_milestones.index',
            'create' => 'admin.journal_milestones.create',
            'edit' => 'admin.journal_milestones.edit',
            'user' => 'admin.journal_milestones.user',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.journal_milestone.index',
            'create' => 'admin.journal_milestone.create',
            'edit' => 'admin.journal_milestone.edit',
            'delete' => 'admin.journal_milestone.delete',
            'user' => 'admin.journal_milestone.user',
        ];
    }

    public function index(JournalMilestoneDataTable $dataTable)
    {
        $rawCounts = JournalMilestone::admin()
            ->selectRaw('stage, count(*) as count')
            ->groupBy('stage')
            ->pluck('count', 'stage')
            ->toArray();

        $stageCounts = [
            MilestoneStage::Newborn->value => (int) ($rawCounts[MilestoneStage::Newborn->value] ?? 0),
            MilestoneStage::Toddler->value => (int) ($rawCounts[MilestoneStage::Toddler->value] ?? 0),
            MilestoneStage::Preschool->value => (int) ($rawCounts[MilestoneStage::Preschool->value] ?? 0),
        ];

        $totalCount = array_sum($stageCounts);
        $currentStage = request('stage');

        return $dataTable->render(
            $this->view['index'],
            [
                'stages' => MilestoneStage::cases(),
                'stageCounts' => $stageCounts,
                'totalCount' => $totalCount,
                'currentStage' => $currentStage,
                'breadcrumbs' => $this->crums->add('DS cột mốc gợi ý'),
            ]
        );
    }

    public function user(UserJournalMilestoneDataTable $dataTable)
    {
        $rawCounts = JournalMilestone::user()
            ->selectRaw('stage, count(*) as count')
            ->groupBy('stage')
            ->pluck('count', 'stage')
            ->toArray();

        $stageCounts = [
            MilestoneStage::Newborn->value => (int) ($rawCounts[MilestoneStage::Newborn->value] ?? 0),
            MilestoneStage::Toddler->value => (int) ($rawCounts[MilestoneStage::Toddler->value] ?? 0),
            MilestoneStage::Preschool->value => (int) ($rawCounts[MilestoneStage::Preschool->value] ?? 0),
        ];

        $totalCount = array_sum($stageCounts);
        $currentStage = request('stage');

        return $dataTable->render(
            $this->view['user'],
            [
                'stages' => MilestoneStage::cases(),
                'stageCounts' => $stageCounts,
                'totalCount' => $totalCount,
                'currentStage' => $currentStage,
                'breadcrumbs' => $this->crums->add('Cột mốc người dùng'),
            ]
        );
    }

    public function create(): Factory|View|Application
    {
        return view($this->view['create'], [
            'stages' => MilestoneStage::cases(),
            'breadcrumbs' => $this->crums->add('DS cột mốc gợi ý', route($this->route['index']))->add('Thêm mới'),
        ]);
    }

    public function store(JournalMilestoneRequest $request): RedirectResponse
    {
        try {
            $response = $this->service->store($request);
            if ($response) {
                return to_route($this->route['index'])->with('success', __('notifySuccess'));
            }
            return back()->with('error', __('notifyFail'));
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit($id): Factory|View|Application
    {
        $response = $this->repository->findOrFail($id);
        return view(
            $this->view['edit'],
            [
                'response' => $response,
                'stages' => MilestoneStage::cases(),
                'breadcrumbs' => $this->crums->add('DS cột mốc gợi ý', route($this->route['index']))->add('Cập nhật'),
            ]
        );
    }

    public function update(JournalMilestoneRequest $request): RedirectResponse
    {
        try {
            $this->service->update($request);
            return back()->with('success', __('notifySuccess'));
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function delete($id): RedirectResponse
    {
        try {
            $this->service->delete($id);
            return redirect()->back()->with('success', __('notifySuccess'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
