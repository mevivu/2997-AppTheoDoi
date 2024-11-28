<?php

namespace App\Admin\Http\Controllers\Setting;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Repositories\Setting\SettingRepositoryInterface;
use App\Enums\Setting\SettingGroup;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function __construct(
        SettingRepositoryInterface $repository
    )
    {
        parent::__construct();
        $this->repository = $repository;
    }
    public function getView(): array
    {
        return [
            'general' => 'admin.settings.general',
            'system' => 'admin.settings.system',
        ];
    }
    public function general(): Factory|View|Application
    {
        $settings = $this->repository->getByGroup([SettingGroup::General]);

        return view($this->view['general'], compact('settings'));
    }



    public function system(): Factory|View|Application
    {
        $settings = $this->repository->getByGroup([SettingGroup::System]);
        return view($this->view['system'], [
            'settings' => $settings
        ]);
    }


    public function update(Request $request): RedirectResponse
    {
        $data = $request->except('_token', '_method');
        $this->repository->updateMultipleRecord($data);
        return back()->with('success', __('notifySuccess'));
    }
}
