<?php

namespace App\Api\V1\Http\Controllers\Setting;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Http\Resources\Setting\SettingResource;
use App\Api\V1\Repositories\Setting\SettingRepositoryInterface;
use App\Enums\Setting\SettingGroup;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * @group Cài đặt
 */

class SettingController extends Controller
{
    use Response, UseLog;

    protected $settingRepository;

    public function __construct(
        SettingRepositoryInterface $settingRepository
    ) {
        $this->settingRepository = $settingRepository;
        $this->middleware('auth:api', ['except' => ['general', 'system', 'c_ride', 'c_car', 'c_delivery', 'c_intercity']]);

    }

    /**
     * Chung
     *
     * Cài đặt chung của hệ thống
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công.",
     *      "data": [
     *         {
     *               "id": 1,
     *               "setting_key": "site_name",
     *               "setting_name": "Tên site",
     *               "plain_value": "Site name",
     *               "desc": "Tên của webiste, shop, app",
     *               "type_input": 1,
     *               "type_data": null,
     *               "group": 1,
     *               "created_at": null,
     *               "updated_at": null
     *           },
     *           {
     *              "id": 2,
     *              "setting_key": "site_logo",
     *              "setting_name": "Logo site",
     *              "plain_value": "/public/assets/images/logo.png",
     *              "desc": "Logo thương hiệu",
     *              "type_input": 7,
     *              "type_data": null,
     *              "group": 1,
     *              "created_at": null,
     *              "updated_at": null
     *           }
     *      ]
     * }
     *
     * @return \Illuminate\Http\Response
     */

    public function general(): JsonResponse
    {
        try {
            $general = $this->settingRepository->getByGroup([SettingGroup::General]);
            $settingResponse = SettingResource::collection($general);

            return $this->jsonResponseSuccess($settingResponse);
        } catch (Exception $e) {
            $this->logError('General setting failed:', $e);
            return $this->jsonResponseError('General setting failed', 500);
        }

    }

    /**
     * Hệ thống
     *
     * Cài đặt hệ thống
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công.",
     *      "data": [
     *         {
     *               "id": 10,
     *               "setting_key": "system_commission_rate",
     *               "setting_name": "Hoa hồng",
     *               "plain_value": "10",
     *               "desc": "Tỉ lệ phần trăm hệ thống nhận được / đơn hàng (10 = 10%)",
     *               "type_input": 2,
     *               "type_data": null,
     *               "group": 4,
     *               "created_at": null,
     *               "updated_at": null
     *           },
     *           {
     *              "id": 11,
     *              "setting_key": "rush_hour_price",
     *              "setting_name": "Phí giờ cao điểm",
     *              "plain_value": "10000",
     *              "desc": "Phí tăng thêm",
     *              "type_input": 11,
     *              "type_data": null,
     *              "group": 4,
     *              "created_at": null,
     *              "updated_at": null
     *           }
     *      ]
     * }
     *
     * @return \Illuminate\Http\Response
     */

    public function system(): JsonResponse
    {
        try {
            $system = $this->settingRepository->getByGroup([SettingGroup::System]);
            $settingResponse = SettingResource::collection($system);

            return $this->jsonResponseSuccess($settingResponse);
        } catch (Exception $e) {
            $this->logError('System setting failed:', $e);
            return $this->jsonResponseError('System setting failed', 500);
        }
    }

    /**
     *  C - Ride
     *
     *  Cài đặt C - Ride
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công.",
     *      "data": [
     *         {
     *               "id": 1,
     *               "setting_key": "c_ride_price",
     *               "setting_name": "Phí cơ bản C - Ride",
     *               "plain_value": "25000",
     *               "desc": "Phí cơ bản ( Giá tham khảo 25.000 vnđ/ km)",
     *               "type_input": 11,
     *               "type_data": null,
     *               "group": 5,
     *               "created_at": null,
     *               "updated_at": null
     *           }
     *      ]
     * }
     *
     * @return \Illuminate\Http\Response
     */

    public function c_ride(): JsonResponse
    {
        try {
            $cRide = $this->settingRepository->getByGroup([SettingGroup::C_Ride]);
            $settingResponse = SettingResource::collection($cRide);

            return $this->jsonResponseSuccess($settingResponse);
        } catch (Exception $e) {
            $this->logError('C_Ride setting failed:', $e);
            return $this->jsonResponseError('C_Ride setting failed', 500);
        }
    }

    /**
     *  C - Car
     *
     * Cài đặt C - Car
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công.",
     *      "data": [
     *         {
     *               "id": 1,
     *               "setting_key": "c_car_price",
     *               "setting_name": "Phí cơ bản C - Car",
     *               "plain_value": "42500",
     *               "desc": "Phí cơ bản ( Giá tham khảo 42.500 vnđ/ km)",
     *               "type_input": 11,
     *               "type_data": null,
     *               "group": 6,
     *               "created_at": null,
     *               "updated_at": null
     *           },
     *
     *      ]
     * }
     *
     * @return \Illuminate\Http\Response
     */

    public function c_car(): JsonResponse
    {

        try {
            $cCar = $this->settingRepository->getByGroup([SettingGroup::C_Car]);
            $settingResponse = SettingResource::collection($cCar);

            return $this->jsonResponseSuccess(($settingResponse));
        } catch (Exception $e) {
            $this->logError('C_Car setting failed:', $e);
            return $this->jsonResponseError('C_Car setting failed', 500);
        }
    }

    /**
     * C - Delivery
     *
     * Cài đặt C - Delivery
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công.",
     *      "data": [
     *         {
     *               "id": 16,
     *               "setting_key": "c_Delivery_option",
     *               "setting_name": "C - Delivery (Bật hoặc tắt)",
     *               "plain_value": "1",
     *               "desc": "Điều chỉnh cước phí riêng từng khối lượng",
     *               "type_input": 9,
     *               "type_data": null,
     *               "group": 7,
     *               "created_at": null,
     *               "updated_at": null
     *           },
     *           {
     *              "id": 17,
     *              "setting_key": "c_Delivery_price",
     *              "setting_name": "Phí cơ bản C - Delivery",
     *              "plain_value": "31620",
     *              "desc": "Phí cơ bản C - Delivery Now & Later ( Giá tham khảo 31.620 vnđ/ km)",
     *              "type_input": 11,
     *              "type_data": null,
     *              "group": 7,
     *              "created_at": null,
     *              "updated_at": null
     *           }
     *      ]
     * }
     *
     * @return \Illuminate\Http\Response
     */

    public function c_delivery(): JsonResponse
    {
        try {
            $cDelivery = $this->settingRepository->getByGroup([SettingGroup::C_Delivery]);
            $settingResponse = SettingResource::collection($cDelivery);

            return $this->jsonResponseSuccess($settingResponse);
        } catch (Exception $e) {
            $this->logError('C_Delivery setting failed:', $e);
            return $this->jsonResponseError('C_Delivery setting failed', 500);
        }
    }

    /**
     * C - Intercity
     *
     * Cài đặt C - Intercity
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công.",
     *      "data": [
     *         {
     *               "id": 24,
     *               "setting_key": "reference_price",
     *               "setting_name": "Giá tham khảo",
     *               "plain_value": "100000",
     *               "desc": "Giá tham khảo 100.000 vnd/người",
     *               "type_input": 11,
     *               "type_data": null,
     *               "group": 8,
     *               "created_at": null,
     *               "updated_at": null
     *           },
     *           {
     *              "id": 25,
     *              "setting_key": "round-trip_price",
     *              "setting_name": "Giá khứ hồi",
     *              "plain_value": "100000",
     *              "desc": "Giá khứ hồi",
     *              "type_input": 11,
     *              "type_data": null,
     *              "group": 7,
     *              "created_at": null,
     *              "updated_at": null
     *           }
     *      ]
     * }
     *
     * @return \Illuminate\Http\Response
     */

    public function c_intercity(): JsonResponse
    {
        try {
            $cIntercity = $this->settingRepository->getByGroup([SettingGroup::C_Intercity]);
            $settingResponse = SettingResource::collection($cIntercity);

            return $this->jsonResponseSuccess($settingResponse);
        } catch (Exception $e) {
            $this->logError('C_Intercity setting failed:', $e);
            return $this->jsonResponseError('C_Intercity setting failed', 500);
        }
    }
}
