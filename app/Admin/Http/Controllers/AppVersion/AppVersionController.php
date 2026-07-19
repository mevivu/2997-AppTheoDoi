<?php

namespace App\Admin\Http\Controllers\AppVersion;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Repositories\AppVersion\AppVersionRepositoryInterface;
use App\Admin\DataTables\AppVersion\AppVersionDataTable;
use App\Traits\ResponseController;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Exception;

class AppVersionController extends Controller
{
    use ResponseController;

    public function __construct(AppVersionRepositoryInterface $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.app_versions.index',
            'edit' => 'admin.app_versions.edit',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.app-version.index',
            'edit' => 'admin.app-version.edit',
            'update' => 'admin.app-version.update',
        ];
    }

    public function index(AppVersionDataTable $dataTable)
    {
        return $dataTable->render($this->view['index'], [
            'breadcrumbs' => $this->crums->add(__('Quản lý Phiên bản')),
        ]);
    }

    public function edit(int $id): View
    {
        $appVersion = $this->repository->findOrFail($id);

        return view($this->view['edit'], [
            'appVersion' => $appVersion,
            'breadcrumbs' => $this->crums->add(
                __('Quản lý Phiên bản'),
                route($this->route['index'])
            )->add(__('Sửa')),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'id' => 'required|integer|exists:app_versions,id',
            'notify' => 'required|string',
            'required' => 'required|string',
            'checking_version' => 'nullable|string',
            'update_url' => 'required|url',
            'release_notes_vi' => 'required|string',
            'release_notes_en' => 'required|string',
            'is_active' => 'required|boolean',
        ]);

        $id = $request->input('id');
        
        $data = [
            'notify' => $request->input('notify'),
            'required' => $request->input('required'),
            'checking_version' => $request->input('checking_version'),
            'update_url' => $request->input('update_url'),
            'release_notes' => [
                'vi' => $request->input('release_notes_vi'),
                'en' => $request->input('release_notes_en'),
            ],
            'is_active' => (bool) $request->input('is_active'),
        ];

        try {
            $this->repository->update($id, $data);
            return redirect()->route($this->route['index'])->with('success', __('Cập nhật phiên bản thành công.'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}
