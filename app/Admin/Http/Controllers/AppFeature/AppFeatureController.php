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
            'enabled_dev' => ['sometimes', 'boolean'],
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
                'enabled_dev' => $feature->enabled_dev,
                'disabled_mode' => $feature->disabled_mode,
                'disabled_message' => $feature->disabled_message,
                'updated_at' => $feature->updated_at?->format('d/m/Y H:i'),
            ],
        ]);
    }

    /**
     * Cập nhật toàn bộ chức năng (khi submit từ form hoặc bấm nút Lưu trên form-actions).
     */
    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'features' => ['required', 'array'],
            'features.*.enabled_android' => ['nullable'],
            'features.*.enabled_ios' => ['nullable'],
            'features.*.enabled_dev' => ['nullable'],
            'features.*.disabled_mode' => ['nullable', Rule::in([AppFeature::MODE_HIDE, AppFeature::MODE_MAINTENANCE])],
            'features.*.disabled_message' => ['nullable', 'string', 'max:255'],
        ]);

        $adminId = auth('admin')->id();
        $updatedCount = 0;

        foreach ($validated['features'] as $id => $item) {
            $feature = AppFeature::find($id);
            if (!$feature) {
                continue;
            }

            $feature->enabled_android = !empty($item['enabled_android']);
            $feature->enabled_ios = !empty($item['enabled_ios']);
            if (array_key_exists('enabled_dev', $item)) {
                $feature->enabled_dev = !empty($item['enabled_dev']);
            }
            if (isset($item['disabled_mode'])) {
                $feature->disabled_mode = $item['disabled_mode'];
            }
            if (array_key_exists('disabled_message', $item)) {
                $feature->disabled_message = $item['disabled_message'];
            }

            if ($feature->isDirty()) {
                $feature->updated_by = $adminId;
                $feature->save();
                $updatedCount++;
            }
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 200,
                'message' => __('notifySuccess'),
                'updated_count' => $updatedCount,
            ]);
        }

        return redirect()->back()->with('success', __('notifySuccess'));
    }
}
