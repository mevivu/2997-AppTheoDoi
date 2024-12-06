<?php

namespace App\Api\V1\Http\Controllers\Clinic;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Http\Requests\Clinic\ClinicRequest;
use App\Api\V1\Http\Requests\Notification\NotificationRequest;
use App\Api\V1\Http\Resources\Clinic\ClinicResourceCollection;
use App\Api\V1\Services\Clinic\ClinicServiceInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use Illuminate\Http\JsonResponse;

/**
 * @group Phòng khám
 */
class ClinicController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    public function __construct(
        ClinicServiceInterface $service

    )
    {
        $this->service = $service;
    }

    /**
     * Tìm kiếm phòng khám
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     * @queryParam name string
     *  Tên phòng khám. Example: Phòng khám 1
     * @queryParam province_id integer
     * Thành phố. Example: 44
     * @queryParam district_id integer
     * Quận. Example:491
     * @queryParam ward_id integer
     * Phường. Example: 8008
     * @queryParam  clinic_type_id integer
     * loại phòng khám. Example: 2
     * @queryParam opening_time string
     * giờ khám. Example: 04:00:00
     * @queryParam page integer
     * Trang hiện tại, page > 0. Example: 1
     * @queryParam limit integer
     * Số lượng thông báo trong 1 trang, limit > 0. Example: 1
     *
     * @authenticated Authorization string required
     * access_token được cấp sau khi đăng nhập. Example: Bearer 1|WhUre3Td7hThZ8sNhivpt7YYSxJBWk17rdndVO8K
     *
     * @response 200 {
     *    "status": 200,
     *    "message": "Thực hiện thành công.",
     *    "data": [
     *{
     * "id": 1,
     * "name": "Phòng khám 1",
     * "address": "37 Phú Châu, An Bình, Thủ Đức",
     * "hotline": "0383476965",
     * "opening_time": "04:52:00",
     * "closing_time": "17:53:00",
     * "clinic_type": "Răng hàm mặt",
     * "province": "Tỉnh Lâm Đồng",
     * "district": "Thành phố Bảo Lộc",
     * "ward": "Phường Lộc Phát"
     * },
     * {
     * "id": 2,
     * "name": "Phòng khám 2",
     * "address": "37 Phú Châu, An Bình, Thủ Đức",
     * "hotline": "0383476965",
     * "opening_time": "11:06:00",
     * "closing_time": "23:06:00",
     * "clinic_type": "Thai sản",
     * "province": "Tỉnh Lâm Đồng",
     * "district": "Huyện Đam Rông",
     * "ward": "Xã Liêng Srônh"
     * }
     *    ]
     * }
     *
     * @param NotificationRequest $request
     *
     * @return JsonResponse
     */
    public function search(ClinicRequest $request)
    {
        try {
            $this->data = $request->validated();
            $clinics = $this->service->search($this->data);
            return $this->jsonResponseSuccess(new ClinicResourceCollection($clinics));
        }catch (\Exception $exception){
            $this->logError('Get Clinic notifications failed:', $exception);
            return $this->jsonResponseError('Get user notifications failed', 500);
        }

    }
}
