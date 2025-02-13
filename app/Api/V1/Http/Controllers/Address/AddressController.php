<?php

namespace App\Api\V1\Http\Controllers\Address;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Repositories\Province\ProvinceRepositoryInterface;
use App\Api\V1\Http\Resources\Province\ProvinceResource;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use Illuminate\Http\JsonResponse;

/**
 * @group Đỉa chỉ
 */
class AddressController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    protected ProvinceRepositoryInterface $provinceRepository;

    public function __construct(
        ProvinceRepositoryInterface $provinceRepository

    )
    {
        $this->provinceRepository = $provinceRepository;
    }

    /**
     * Lấy DS tỉnh thành phố
     *
     * @response 200 {
     *    "status": true,
     *    "message": "Danh sách tỉnh thành phố",
     *    "data": [
     *        {
     *            "id": 1,
     *            "name": "Răng hàm mặt",
     *            "description": "Chuyên khoa răng hàm mặt"
     *        },
     *        {
     *            "id": 2,
     *            "name": "Thai sản",
     *            "description": "Chuyên khoa thai sản"
     *        }
     *    ]
     * }
     *
     * @return JsonResponse
     */
    public function getProvinces(): JsonResponse
    {
        $response = $this->provinceRepository->getAll();
        return $this->jsonResponseSuccess(ProvinceResource::collection($response));
    }


}
