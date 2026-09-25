<?php

namespace App\Admin\Http\Controllers\Memo;

use App\Admin\DataTables\Memo\MemoCompetitionDataTable;
use App\Admin\DataTables\Memo\MemoCompetitionLeaderboardDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Memo\MemoCompetitionRequest;
use App\Admin\Repositories\MemoCompetition\MemoCompetitionRepositoryInterface;
use App\Admin\Services\MemoCompetition\MemoCompetitionServiceInterface;
use App\Enums\ActiveStatus;
use App\Models\MemoAgeConfig;
use App\Models\MemoCompetitionEntry;
use App\Models\MemoTheme;
use App\Traits\RouteAdminSystem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MemoCompetitionController extends Controller
{
    public function __construct(
        MemoCompetitionRepositoryInterface $repository,
        MemoCompetitionServiceInterface $service
    ) {
        parent::__construct();
        $this->repository = $repository;
        $this->service = $service;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.memo-game.competition.index',
            'create' => 'admin.memo-game.competition.create',
            'edit' => 'admin.memo-game.competition.edit',
            'leaderboard' => 'admin.memo-game.competition.leaderboard',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => RouteAdminSystem::MEMO_COMPETITION_INDEX,
            'create' => RouteAdminSystem::MEMO_COMPETITION_CREATE,
            'edit' => RouteAdminSystem::MEMO_COMPETITION_EDIT,
            'delete' => RouteAdminSystem::MEMO_COMPETITION_DELETE,
        ];
    }

    /**
     * Danh sách các giải đấu (Sử dụng DataTable chuẩn hệ thống)
     */
    public function index(MemoCompetitionDataTable $dataTable)
    {
        return $dataTable->render(
            $this->view['index'],
            [
                'breadcrumbs' => $this->crums
                    ->add('Memo Game (Trí nhớ)', route(RouteAdminSystem::MEMO_THEME_INDEX))
                    ->add('Giải đấu & Cuộc thi'),
            ]
        );
    }

    /**
     * Màn hình tạo giải đấu mới
     */
    public function create()
    {
        $themes = MemoTheme::where('status', ActiveStatus::Active->value)
            ->with(['activeCards'])
            ->withCount(['cards' => function ($q) {
                $q->where('status', ActiveStatus::Active->value);
            }])
            ->orderBy('position', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        $ageConfigs = MemoAgeConfig::where('status', ActiveStatus::Active->value)
            ->competition()
            ->orderBy('rows', 'asc')
            ->orderBy('columns', 'asc')
            ->get();

        // Ưu tiên chọn cấu hình 5x6
        $defaultConfig = $ageConfigs->first(function ($c) {
            return $c->rows == 5 && $c->columns == 6;
        }) ?: $ageConfigs->first();

        return view($this->view['create'], [
            'themes' => $themes,
            'ageConfigs' => $ageConfigs,
            'defaultConfigId' => $defaultConfig?->id,
            'breadcrumbs' => $this->crums
                ->add('Memo Game (Trí nhớ)', route(RouteAdminSystem::MEMO_THEME_INDEX))
                ->add('Giải đấu', route(RouteAdminSystem::MEMO_COMPETITION_INDEX))
                ->add('Tạo giải đấu mới'),
        ]);
    }

    /**
     * Lưu giải đấu mới (Ủy quyền xử lý cho Service)
     */
    public function store(MemoCompetitionRequest $request): RedirectResponse
    {
        try {
            $competition = $this->service->store($request);

            if ($competition) {
                return redirect()->route($this->route['index'])
                    ->with('success', 'Tạo giải đấu Memo Game thành công!');
            }

            return back()->withInput()->with('error', 'Không thể tạo giải đấu, vui lòng thử lại.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Lỗi khi tạo giải đấu: ' . $e->getMessage());
        }
    }

    /**
     * Màn hình cập nhật giải đấu
     */
    public function edit(int $id)
    {
        $competition = $this->repository->findWithThemes($id);

        $themes = MemoTheme::where('status', ActiveStatus::Active->value)
            ->with(['activeCards'])
            ->withCount(['cards' => function ($q) {
                $q->where('status', ActiveStatus::Active->value);
            }])
            ->orderBy('position', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        $ageConfigs = MemoAgeConfig::where('status', ActiveStatus::Active->value)
            ->competition()
            ->orderBy('rows', 'asc')
            ->orderBy('columns', 'asc')
            ->get();

        $selectedThemes = $competition->competitionThemes->pluck('memo_theme_id')->toArray();

        return view($this->view['edit'], [
            'competition' => $competition,
            'themes' => $themes,
            'ageConfigs' => $ageConfigs,
            'selectedThemes' => $selectedThemes,
            'breadcrumbs' => $this->crums
                ->add('Memo Game (Trí nhớ)', route(RouteAdminSystem::MEMO_THEME_INDEX))
                ->add('Giải đấu', route(RouteAdminSystem::MEMO_COMPETITION_INDEX))
                ->add('Chỉnh sửa giải đấu'),
        ]);
    }

    /**
     * Cập nhật thông tin giải đấu (Ủy quyền xử lý cho Service)
     */
    public function update(MemoCompetitionRequest $request): RedirectResponse
    {
        try {
            $this->service->update($request);
            return back()->with('success', 'Cập nhật thông tin giải đấu thành công!');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Lỗi khi cập nhật giải đấu: ' . $e->getMessage());
        }
    }

    /**
     * Xóa giải đấu (Ủy quyền xử lý cho Service)
     */
    public function delete(int $id): RedirectResponse
    {
        try {
            $this->service->delete($id);
            return redirect()->route($this->route['index'])
                ->with('success', 'Đã xóa giải đấu thành công.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Lỗi khi xóa giải đấu: ' . $e->getMessage());
        }
    }

    /**
     * Bảng xếp hạng chi tiết của giải đấu
     */
    public function leaderboard(int $id, MemoCompetitionLeaderboardDataTable $dataTable)
    {
        $competition = $this->repository->findWithThemes($id);
        $leaderboard = $this->repository->getLeaderboard($id, 100);

        $totalUniqueChildren = MemoCompetitionEntry::where('memo_competition_id', $id)
            ->distinct('child_id')
            ->count('child_id');

        $totalAttempts = MemoCompetitionEntry::where('memo_competition_id', $id)->count();

        $completedCount = MemoCompetitionEntry::where('memo_competition_id', $id)
            ->where('is_valid', true)
            ->count();

        $bestRecord = $leaderboard->first();

        return $dataTable->with('competitionId', $id)->render($this->view['leaderboard'], [
            'competition' => $competition,
            'leaderboard' => $leaderboard,
            'totalParticipants' => $totalUniqueChildren,
            'totalAttempts' => $totalAttempts,
            'completedCount' => $completedCount,
            'bestRecord' => $bestRecord,
            'isFinal' => (bool) $competition->ranking_calculated_at,
            'breadcrumbs' => $this->crums
                ->add('Memo Game (Trí nhớ)', route(RouteAdminSystem::MEMO_THEME_INDEX))
                ->add('Giải đấu', route(RouteAdminSystem::MEMO_COMPETITION_INDEX))
                ->add($competition->name, route(RouteAdminSystem::MEMO_COMPETITION_EDIT, $competition->id))
                ->add('Bảng xếp hạng'),
        ]);
    }

    /**
     * Tính toán và chốt bảng xếp hạng khi giải đấu kết thúc (Ủy quyền cho Service)
     */
    public function calculateRankings(int $id): RedirectResponse
    {
        try {
            $updated = $this->service->calculateRankings($id);
            return back()->with('success', "Đã tính toán và chốt bảng xếp hạng giải đấu thành công ({$updated} lượt thi)!");
        } catch (\Throwable $e) {
            return back()->with('error', 'Lỗi khi tính toán bảng xếp hạng: ' . $e->getMessage());
        }
    }
}
