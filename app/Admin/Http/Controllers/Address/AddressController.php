<?php

namespace App\Admin\Http\Controllers\Address;

use App\Admin\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Province;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * Handle the event of province selection and retrieve corresponding districts.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getDistrictsByProvince(Request $request):JsonResponse
    {

        $province = Province::where('code', $request->province_code)->first();

        if (!$province) {
            return response()->json(['message' => 'Province not found'], 404);
        }

        $districts = $province->district()->get();

        return response()->json($districts);
    }

    public function getWardsByDistrict(Request $request): JsonResponse
    {

        $district = District::where('code', $request->district_code)->first();

        if (!$district) {
            return response()->json(['message' => 'District not found'], 404);
        }

        $wards = $district->wards()->get();

        return response()->json($wards);
    }

}
