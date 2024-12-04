<?php

namespace App\Admin\Http\Controllers\Slider;

use App\Admin\Http\Controllers\Controller;
use App\Admin\DataTables\Slider\SliderItemDataTable;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use App\Admin\Repositories\Slider\{SliderRepositoryInterface, SliderItemRepositoryInterface};
use App\Admin\Services\Slider\SliderItemServiceInterface;
use App\Admin\Http\Requests\Slider\SliderItemRequest;

class SliderItemController extends Controller
{
    protected SliderRepositoryInterface $repositorySlider;

    public function __construct(
        SliderItemRepositoryInterface $repository,
        SliderRepositoryInterface     $repositorySlider,
        SliderItemServiceInterface    $service
    )
    {
        parent::__construct();
        $this->repository = $repository;
        $this->repositorySlider = $repositorySlider;
        $this->service = $service;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.sliders.items.index',
            'create' => 'admin.sliders.items.create',
            'edit' => 'admin.sliders.items.edit'
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.slider.item.index',
            'create' => 'admin.slider.item.create',
            'edit' => 'admin.slider.item.edit',
            'delete' => 'admin.slider.item.delete'
        ];
    }

    public function index($slider_id, SliderItemDataTable $dataTable)
    {
        $slider = $this->repositorySlider->findOrFail($slider_id);
        return $dataTable->with('slider', $slider)->render($this->view['index'], [
            'slider' => $slider,
            'breadcrumbs' => $this->crums->add(__('SliderItem'))
        ]);
    }

    /**
     * @throws \Exception
     */
    public function create($slider_id): Factory|View|Application
    {
        $slider = $this->repositorySlider->findOrFail($slider_id);
        return view($this->view['create'], [
            'slider' => $slider,
            'breadcrumbs' => $this->crums->add(__('SliderItem'))
        ]);
    }

    public function store(SliderItemRequest $request): RedirectResponse
    {
        $response = $this->service->store($request);
        if ($response) {
            return to_route($this->route['index'], $response->slider_id)->with('success', __('notifySuccess'));
        }
        return back()->with('error', __('notifyFail'))->withInput();
    }

    public function edit($id): Factory|View|Application
    {
        $sliderItem = $this->repository->findOrFailWithRelations($id);
        return view($this->view['edit'], [
            'sliderItem' => $sliderItem,
            'breadcrumbs' => $this->crums->add(__('SliderItem'))
        ]);
    }

    public function update(SliderItemRequest $request): RedirectResponse
    {
        $response = $this->service->update($request);
        if ($response) {
            return to_route($this->route['index'], $response->slider_id)->with('success', __('notifySuccess'));
        }
        return back()->with('error', __('notifyFail'))->withInput();
    }

    public function delete($slider_id, $id): RedirectResponse
    {

        $this->service->delete($id);

        return to_route($this->route['index'], $slider_id)->with('success', __('notifySuccess'));

    }
}
