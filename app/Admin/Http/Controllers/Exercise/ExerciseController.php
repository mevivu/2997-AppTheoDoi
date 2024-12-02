<?php

namespace App\Admin\Http\Controllers\Exercise;

use App\Admin\DataTables\Exercise\ExercisePhysicalDataTable;
use App\Admin\DataTables\Exercise\ExercisePowerDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Exercise\ExerciseRequest;
use App\Admin\Repositories\Exercise\ExerciseRepositoryInterface;
use App\Admin\Services\Exercise\ExerciseServiceInterface;
use App\Enums\ActiveStatus;
use App\Enums\Exercise\ExerciseType;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;


class ExerciseController extends Controller
{
    public function __construct(
        ExerciseRepositoryInterface $repository,
        ExerciseServiceInterface $service
    ) {

        parent::__construct();

        $this->repository = $repository;


        $this->service = $service;

    }

    public function getView(): array
    {
        return [
            'physical' => 'admin.exercise.physical',
            'power' => 'admin.exercise.power',
            'create' => 'admin.exercise.create',
            'edit' => 'admin.exercise.edit'
        ];
    }

    public function getRoute(): array
    {
        return [
            'physical' => 'admin.exercise.physical',
            'power' => 'admin.exercise.power',
            'create' => 'admin.exercise.create',
            'edit' => 'admin.exercise.edit',
            'delete' => 'admin.exercise.delete'
        ];
    }

    public function physical(ExercisePhysicalDataTable $dataTable)
    {
        return $dataTable->render(
            $this->view['physical'],
            [
                'actionMultiple' => $this->getActionMultiple(),
                'breadcrums' => $this->crums->add('Danh sách bài tập thể lực'),
            ]
        );
    }

    public function power(ExercisePowerDataTable $dataTable)
    {
        return $dataTable->render(
            $this->view['power'],
            [
                'actionMultiple' => $this->getActionMultiple(),
                'breadcrums' => $this->crums->add('Danh sách bài tập sức mạnh'),
            ]
        );
    }


    public function create(): Factory|View|Application
    {
        return view($this->view['create'], [
            'status' => ActiveStatus::asSelectArray(),
            'types' => ExerciseType::asSelectArray(),
            'breadcrumbs' => $this->crums->add('Bài tập')->add('Thêm mới'),
        ]);
    }

    public function store(ExerciseRequest $request): RedirectResponse
    {
        $response = $this->service->store($request);
        if ($response) {
            return to_route($this->route['edit'], $response)->with('success', __('notifySuccess'));
        }
        return back()->with('error', __('notifyFail'));
    }

    /**
     * @throws Exception
     */
    public function edit($id): Factory|View|Application
    {
        $response = $this->repository->findOrFail($id);
        return view(
            $this->view['edit'],
            [
                'response' => $response,
                'status' => ActiveStatus::asSelectArray(),
                'types' => ExerciseType::asSelectArray(),
                'breadcrumbs' => $this->crums->add('Bài tập')->add('Cập nhật'),
            ]
        );

    }

    public function update(ExerciseRequest $request): RedirectResponse
    {

        $this->service->update($request);
        return back()->with('success', __('notifySuccess'));

    }

    public function delete($id): RedirectResponse
    {

        $this->repository->delete($id);
        return redirect()->back()->with('success', __('notifySuccess'));

    }

    protected function getActionMultiple(): array
    {
        return [
            ActiveStatus::Active->value => ActiveStatus::Active->description(),
            ActiveStatus::Draft->value => ActiveStatus::Draft->description(),
            ActiveStatus::Deleted->value => ActiveStatus::Deleted->description(),
        ];
    }

    public function actionMultipleRecords(Request $request)
    {
        $boolean = $this->service->actionMultipleRecords($request);
        if ($boolean) {
            return back()->with('success', __('notifySuccess'));
        }
        return back()->with('error', __('notifyFail'));
    }
}