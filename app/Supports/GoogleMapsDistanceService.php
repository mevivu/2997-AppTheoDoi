<?php

use Illuminate\Support\Facades\Http;

if (!function_exists('calculate_distance')) {
    // Hàm tính khoảng cách giữa hai điểm trên mặt phẳng
    function calculate_distance($lat1, $lng1, $lat2, $lng2): float|int
    {
        $earthRadius = 6371;

        // Đổi độ sang radian
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        // Áp dụng công thức Haversine
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLng / 2) * sin($dLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;

    }

    if (!function_exists('calculateDistanceGoogleAPi')) {
        /**
         * Tính khoảng cách và thời gian từ điểm xuất phát đến điểm đến sử dụng Google Maps API.
         *
         * @param float $originLat Vĩ độ điểm xuất phát
         * @param float $originLng Kinh độ điểm xuất phát
         * @param float $destLat Vĩ độ điểm đến
         * @param float $destLng Kinh độ điểm đến
         * @return float|string Khoảng cách tính bằng km hoặc thông báo lỗi
         */
        function calculateDistanceGoogleAPi(float $originLat, float $originLng, float $destLat, float $destLng): float|string
        {
            $apiKey = config('services.google_maps.api_key');
            $origin = "{$originLat},{$originLng}";
            $destination = "{$destLat},{$destLng}";

            $response = Http::get("https://maps.googleapis.com/maps/api/distancematrix/json", [
                'origins' => $origin,
                'destinations' => $destination,
                'key' => $apiKey,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['rows'][0]['elements'][0]['distance'])) {
                    $distanceMeters = $data['rows'][0]['elements'][0]['distance']['value'];
                    $distanceKilometers = $distanceMeters / 1000;
                    return round($distanceKilometers, 1);
                }
            }

            return 'Không thể tính toán khoảng cách.';
        }
    }

    if (!function_exists('find_closest_driver')) {
        /**
         * Tìm tài xế gần nhất với cửa hàng.
         *
         * @param float $storeLat Vĩ độ của cửa hàng
         * @param float $storeLng Kinh độ của cửa hàng
         * @param iterable $drivers Danh sách các tài xế, mỗi tài xế là một đối tượng với các thuộc tính 'current_lat' và 'current_lng'
         * @return mixed Trả về tài xế gần nhất hoặc null nếu không tìm thấy
         */
        function find_closest_driver(float $storeLat, float $storeLng, iterable $drivers): mixed
        {
            $closestDriver = null;
            $minDistance = PHP_INT_MAX;

            foreach ($drivers as $driver) {
                if (!isset($driver->current_lat) || !isset($driver->current_lng)) {
                    continue;
                }
                $driverLat = $driver->current_lat;
                $driverLng = $driver->current_lng;

                $distance = calculate_distance($storeLat, $storeLng, $driverLat, $driverLng);

                if ($distance < $minDistance) {
                    $minDistance = $distance;
                    $closestDriver = $driver;
                }
            }

            return $closestDriver;
        }
    }

    /**
     * Lấy tọa độ trung tâm của khu vực từ tên khu vực.
     *
     * @param string $areaName Tên khu vực
     * @return array|null Tọa độ trung tâm của khu vực hoặc null nếu không tìm thấy
     */
    function getAreaCoordinatesByName(string $areaName): ?array
    {
        $apiKey = config('services.google_maps.api_key');
        $response = Http::get("https://maps.googleapis.com/maps/api/geocode/json", [
            'address' => $areaName,
            'key' => $apiKey
        ]);

        if ($response->successful()) {
            $data = $response->json();
            if (!empty($data['results'][0]['geometry']['location'])) {
                return $data['results'][0]['geometry']['location'];
            }
        }
        return null;
    }

    /**
     * Kiểm tra xem một tọa độ có nằm trong khu vực  hay không.
     *
     * @param float $lat Vĩ độ của tọa độ cần kiểm tra
     * @param float $lng Kinh độ của tọa độ cần kiểm tra
     * @param array|null $bounds
     * @return bool True nếu tọa độ nằm trong khu vực, False nếu không nằm trong khu vực
     */
    function isCoordinateInArea(float $lat, float $lng, ?array $bounds): bool
    {
        if (!$bounds || !isset($bounds['northeast'], $bounds['southwest'])) {
            return false;
        }

        $ne = $bounds['northeast'];
        $sw = $bounds['southwest'];

        return $lat <= $ne['lat'] && $lat >= $sw['lat'] && $lng <= $ne['lng'] && $lng >= $sw['lng'];
    }

}

if (!function_exists('getBoundsByName')) {
    /**
     * Lấy khung giới hạn cho một địa điểm cụ thể bằng cách sử dụng Google Geocoding API.
     *
     * @param string $name Tên địa điểm cần truy vấn.
     * @return array|null Mảng khung giới hạn hoặc null nếu không tìm thấy.
     */
    function getBoundsByName(string $name): ?array
    {
        $apiKey = config('services.google_maps.api_key');
        $encodedName = urlencode($name);
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$encodedName}&key={$apiKey}";

        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json();
            if (!empty($data['results']) && isset($data['results'][0]['geometry']['bounds'])) {
                return $data['results'][0]['geometry']['bounds'];
            } else {
                return null;
            }
        }

        return null;
    }
}

