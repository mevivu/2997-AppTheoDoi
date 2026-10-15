<?php

namespace App\Admin\Http\Controllers\AppFeature;

use App\Admin\Http\Controllers\Controller;
use App\Models\AppFeature;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AppFeatureController extends Controller
{
    public function index(): View
    {
        $features = AppFeature::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        // Icon/màu lấy từ catalog thống kê (nếu có) để đồng bộ giao diện admin
        $catalog = \App\Admin\Services\FeatureStatisticsService::getFeatureCatalog();

        return view('admin.settings.app-features', [
            'groups' => AppFeature::GROUPS,
            'features' => $features->groupBy('group'),
            'catalog' => $catalog,
            'total' => $features->count(),
        ]);
    }

    /**
     * Cập nhật 1 chức năng (gọi bằng AJAX từ trang quản lý).
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $feature = AppFeature::findOrFail($id);

        $data = $request->validate([
            'enabled_android' => ['sometimes', 'boolean'],
            'enabled_ios' => ['sometimes', 'boolean'],
            'disabled_mode' => ['sometimes', Rule::in([AppFeature::MODE_HIDE, AppFeature::MODE_MAINTENANCE])],
            'disabled_message' => ['sometimes', 'nullable', 'string', 'max:255'],
        ]);

        $feature->fill($data);
        $old = $feature->getOriginal();
        $dirty = $feature->getDirty();

        if (!empty($dirty)) {
            $feature->updated_by = auth('admin')->id();
            $feature->save();

            // Audit: ai đổi gì (không chứa dữ liệu nhạy cảm)
            Log::info('[AppFeature] updated', [
                'admin_id' => auth('admin')->id(),
                'code' => $feature->code,
                'changes' => collect($dirty)->only(array_keys($data))->map(fn ($new, $key) => [
                    'old' => $old[$key] ?? null,
                    'new' => $new,
                ])->all(),
            ]);
        }

        return response()->json([
            'status' => 200,
            'message' => __('notifySuccess'),
            'data' => [
                'id' => $feature->id,
                'enabled_android' => $feature->enabled_android,
                'enabled_ios' => $feature->enabled_ios,
                'disabled_mode' => $feature->disabled_mode,
                'disabled_message' => $feature->disabled_message,
                'updated_at' => $feature->updated_at?->format('d/m/Y H:i'),
            ],
        ]);
    }
}
