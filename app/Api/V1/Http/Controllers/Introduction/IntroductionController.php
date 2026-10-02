<?php

namespace App\Api\V1\Http\Controllers\Introduction;

use App\Api\V1\Http\Resources\Introduction\IntroductionResource;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use App\Enums\DefaultStatus;
use App\Http\Controllers\Controller;
use App\Models\Introduction;
use App\Traits\MessageSystem;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Group Giới thiệu nền tảng & Thương hiệu
 */
class IntroductionController extends Controller
{
    use Response, UseLog;

    /**
     * Danh sách bài giới thiệu
     *
     * Lấy danh sách các bài giới thiệu, tầm nhìn, sứ mệnh, giá trị cốt lõi
     *
     * @queryParam section_type int Lọc theo phân loại (1: Giới thiệu chung, 2: Tầm nhìn, 3: Sứ mệnh, 4: Giá trị cốt lõi, 5: Thương hiệu). Example: 1
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Introduction::query()->where('status', DefaultStatus::Published);

            if ($request->filled('section_type')) {
                $query->where('section_type', $request->input('section_type'));
            }

            $items = $query->orderBy('sort_order', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            return $this->jsonResponseSuccess(IntroductionResource::collection($items));
        } catch (Exception $e) {
            $this->logError('Get introductions failed: ', $e);
            return $this->jsonResponseError(MessageSystem::SERVER_ERROR, 500);
        }
    }

    /**
     * Chi tiết bài giới thiệu
     *
     * @param mixed $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $item = Introduction::query()
                ->where('status', DefaultStatus::Published)
                ->where(function ($q) use ($id) {
                    if (is_numeric($id)) {
                        $q->where('id', $id);
                    } else {
                        $q->where('slug', $id);
                    }
                })
                ->first();

            if (!$item) {
                return $this->jsonResponseError('Không tìm thấy thông tin giới thiệu', 404);
            }

            return $this->jsonResponseSuccess(new IntroductionResource($item));
        } catch (Exception $e) {
            $this->logError('Get introduction detail failed: ', $e);
            return $this->jsonResponseError(MessageSystem::SERVER_ERROR, 500);
        }
    }
}
