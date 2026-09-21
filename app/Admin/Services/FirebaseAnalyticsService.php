<?php

namespace App\Admin\Services;

use Google\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseAnalyticsService
{
    protected ?string $propertyId;
    protected string $credentialsPath;

    public function __construct()
    {
        $this->propertyId = config('services.firebase.analytics_property_id');
        $this->credentialsPath = base_path('firebase_credentials.json');
    }

    /**
     * Get authorized Guzzle HTTP client for Google API requests.
     */
    protected function getAuthorizedClient(): ?\GuzzleHttp\Client
    {
        if (!file_exists($this->credentialsPath)) {
            Log::warning('Firebase credentials file not found at: ' . $this->credentialsPath);
            return null;
        }

        try {
            $client = new Client();
            $client->setAuthConfig($this->credentialsPath);
            $client->addScope('https://www.googleapis.com/auth/analytics.readonly');
            
            return $client->authorize();
        } catch (\Exception $e) {
            Log::error('Failed to authorize Google Client for Analytics: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Fetch active users over time (1d, 7d, 30d).
     */
    public function getActiveUsersOverTime(string $period = '30d'): array
    {
        if (empty($this->propertyId)) {
            throw new \Exception('Chưa cấu hình FIREBASE_ANALYTICS_PROPERTY_ID trong file .env');
        }

        $httpClient = $this->getAuthorizedClient();
        if (!$httpClient) {
            throw new \Exception('Không thể xác thực Google Client. Vui lòng kiểm tra file firebase_credentials.json ở thư mục gốc.');
        }

        try {
            $url = "https://analyticsdata.googleapis.com/v1beta/properties/{$this->propertyId}:runReport";
            
            $days = 30;
            $dimensionName = 'date';
            
            if ($period === '1d') {
                $days = 1;
                $dimensionName = 'dateHour';
            } elseif ($period === '7d') {
                $days = 7;
                $dimensionName = 'date';
            } elseif ($period === '90d' || $period === '3m') {
                $days = 90;
                $dimensionName = 'date';
            }

            $response = $httpClient->post($url, [
                'json' => [
                    'dateRanges' => [
                        ['startDate' => "{$days}daysAgo", 'endDate' => 'today']
                    ],
                    'dimensions' => [
                        ['name' => $dimensionName]
                    ],
                    'metrics' => [
                        ['name' => 'activeUsers']
                    ],
                    'orderBys' => [
                        [
                            'dimension' => ['dimensionName' => $dimensionName],
                            'desc' => false
                        ]
                    ]
                ]
            ]);

            if ($response->getStatusCode() === 200) {
                $data = json_decode($response->getBody()->getContents(), true);
                return $this->parseOverTimeData($data);
            }
            
            $errorBody = $response->getBody()->getContents();
            Log::error('Google Analytics API error: ' . $errorBody);
            
            $errData = json_decode($errorBody, true);
            $errMsg = $errData['error']['message'] ?? $errorBody;
            throw new \Exception('Google Analytics API Error: ' . $errMsg);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $response = $e->getResponse();
            $errorMsg = $response ? $response->getBody()->getContents() : $e->getMessage();
            Log::error('RequestException in getActiveUsersOverTime: ' . $errorMsg);
            
            $errData = json_decode($errorMsg, true);
            $cleanMsg = $errData['error']['message'] ?? $errorMsg;
            throw new \Exception('Google Analytics API Error: ' . $cleanMsg);
        } catch (\Exception $e) {
            Log::error('Exception in getActiveUsersOverTime: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Fetch active users by app version.
     */
    public function getActiveUsersByVersion(int $days = 30): array
    {
        if (empty($this->propertyId)) {
            throw new \Exception('Chưa cấu hình FIREBASE_ANALYTICS_PROPERTY_ID trong file .env');
        }

        $httpClient = $this->getAuthorizedClient();
        if (!$httpClient) {
            throw new \Exception('Không thể xác thực Google Client. Vui lòng kiểm tra file firebase_credentials.json ở thư mục gốc.');
        }

        try {
            $url = "https://analyticsdata.googleapis.com/v1beta/properties/{$this->propertyId}:runReport";
            
            $response = $httpClient->post($url, [
                'json' => [
                    'dateRanges' => [
                        ['startDate' => "{$days}daysAgo", 'endDate' => 'today']
                    ],
                    'dimensions' => [
                        ['name' => 'appVersion']
                    ],
                    'metrics' => [
                        ['name' => 'activeUsers']
                    ]
                ]
            ]);

            if ($response->getStatusCode() === 200) {
                $data = json_decode($response->getBody()->getContents(), true);
                return $this->parseVersionData($data);
            }
            
            $errorBody = $response->getBody()->getContents();
            Log::error('Google Analytics API version error: ' . $errorBody);
            
            $errData = json_decode($errorBody, true);
            $errMsg = $errData['error']['message'] ?? $errorBody;
            throw new \Exception('Google Analytics API Error: ' . $errMsg);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $response = $e->getResponse();
            $errorMsg = $response ? $response->getBody()->getContents() : $e->getMessage();
            Log::error('RequestException in getActiveUsersByVersion: ' . $errorMsg);
            
            $errData = json_decode($errorMsg, true);
            $cleanMsg = $errData['error']['message'] ?? $errorMsg;
            throw new \Exception('Google Analytics API Error: ' . $cleanMsg);
        } catch (\Exception $e) {
            Log::error('Exception in getActiveUsersByVersion: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Parse raw response from Google Analytics Data API for date series.
     */
    protected function parseOverTimeData(array $data): array
    {
        $result = [];
        if (!isset($data['rows'])) {
            return $result;
        }

        foreach ($data['rows'] as $row) {
            $rawDate = $row['dimensionValues'][0]['value'] ?? '';
            $activeUsers = (int)($row['metricValues'][0]['value'] ?? 0);

            $formattedDate = '';
            if (strlen($rawDate) === 10) {
                // dateHour: YYYYMMDDHH -> e.g. "HH:00"
                $formattedDate = substr($rawDate, 8, 2) . 'h';
            } elseif (strlen($rawDate) === 8) {
                // date: YYYYMMDD -> DD/MM
                $formattedDate = substr($rawDate, 6, 2) . '/' . substr($rawDate, 4, 2);
            } else {
                $formattedDate = $rawDate;
            }

            $result[] = [
                'date' => $formattedDate,
                'users' => $activeUsers
            ];
        }

        return $result;
    }

    /**
     * Parse raw response from Google Analytics Data API for version series.
     */
    protected function parseVersionData(array $data): array
    {
        $result = [];
        if (!isset($data['rows'])) {
            return $result;
        }

        foreach ($data['rows'] as $row) {
            $version = $row['dimensionValues'][0]['value'] ?? 'Unknown';
            $activeUsers = (int)($row['metricValues'][0]['value'] ?? 0);

            $result[] = [
                'version' => $version,
                'users' => $activeUsers
            ];
        }

        return $result;
    }

    /**
     * Elegant Mock data for Active Users over time.
     */
    protected function getMockActiveUsersOverTime(string $period): array
    {
        $result = [];
        if ($period === '1d') {
            $startDate = now()->subDay();
            for ($i = 0; $i < 24; $i++) {
                $date = $startDate->copy()->addHours($i);
                $result[] = [
                    'date' => $date->format('H') . 'h',
                    'users' => rand(5, 30)
                ];
            }
        } elseif ($period === '7d') {
            $startDate = now()->subDays(7);
            for ($i = 0; $i < 7; $i++) {
                $date = $startDate->copy()->addDays($i);
                $result[] = [
                    'date' => $date->format('d/m'),
                    'users' => rand(30, 80)
                ];
            }
        } elseif ($period === '90d' || $period === '3m') {
            $startDate = now()->subDays(90);
            for ($i = 0; $i < 90; $i++) {
                $date = $startDate->copy()->addDays($i);
                $result[] = [
                    'date' => $date->format('d/m'),
                    'users' => rand(50, 150) + (int)(sin($i / 2) * 20)
                ];
            }
        } else {
            $startDate = now()->subDays(30);
            for ($i = 0; $i < 30; $i++) {
                $date = $startDate->copy()->addDays($i);
                $result[] = [
                    'date' => $date->format('d/m'),
                    'users' => rand(50, 150) + (int)(sin($i / 2) * 20)
                ];
            }
        }
        return $result;
    }

    /**
     * Lấy thống kê phân bố hệ điều hành thiết bị (Android vs iOS).
     */
    public function getDeviceOsStats(string $period = '30d'): array
    {
        $cacheKey = 'firebase_device_os_stats_' . $period;
        return \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addMinutes(15), function () use ($period) {
            $days = 30;
            if ($period === '7d') {
                $days = 7;
            } elseif ($period === '1d') {
                $days = 1;
            } elseif ($period === '90d' || $period === '3m') {
                $days = 90;
            }

            $httpClient = $this->getAuthorizedClient();
            if (empty($this->propertyId) || !$httpClient) {
                return $this->getFallbackDeviceOsStats();
            }

            try {
                $url = "https://analyticsdata.googleapis.com/v1beta/properties/{$this->propertyId}:runReport";

                $response = $httpClient->post($url, [
                    'json' => [
                        'dateRanges' => [
                            ['startDate' => "{$days}daysAgo", 'endDate' => 'today']
                        ],
                        'dimensions' => [
                            ['name' => 'operatingSystem']
                        ],
                        'metrics' => [
                            ['name' => 'activeUsers']
                        ],
                        'orderBys' => [
                            [
                                'metric' => ['metricName' => 'activeUsers'],
                                'desc' => true
                            ]
                        ]
                    ]
                ]);

                if ($response->getStatusCode() === 200) {
                    $data = json_decode($response->getBody()->getContents(), true);
                    return $this->parseDeviceOsData($data, $period);
                }
            } catch (\Exception $e) {
                Log::warning('Firebase getDeviceOsStats API error: ' . $e->getMessage());
            }

            return $this->getFallbackDeviceOsStats();
        });
    }

    /**
     * Phân tích và chuẩn hóa dữ liệu hệ điều hành thiết bị.
     */
    protected function parseDeviceOsData(array $data, string $period = '30d'): array
    {
        $iosUsers = 0;
        $androidUsers = 0;
        $otherUsers = 0;

        if (isset($data['rows']) && is_array($data['rows'])) {
            foreach ($data['rows'] as $row) {
                $osName = trim($row['dimensionValues'][0]['value'] ?? '');
                $count = (int)($row['metricValues'][0]['value'] ?? 0);

                if (strcasecmp($osName, 'iOS') === 0) {
                    $iosUsers += $count;
                } elseif (strcasecmp($osName, 'Android') === 0) {
                    $androidUsers += $count;
                } else {
                    $otherUsers += $count;
                }
            }
        }

        $totalUsers = $iosUsers + $androidUsers + $otherUsers;
        $effectiveTotal = ($iosUsers + $androidUsers) > 0 ? ($iosUsers + $androidUsers) : ($totalUsers > 0 ? $totalUsers : 1);

        $iosPercent = round(($iosUsers / $effectiveTotal) * 100, 1);
        $androidPercent = round(($androidUsers / $effectiveTotal) * 100, 1);

        if ($iosUsers > 0 || $androidUsers > 0) {
            $androidPercent = round(100 - $iosPercent, 1);
        }

        $topPlatform = $iosUsers >= $androidUsers ? 'iOS' : 'Android';
        $topPercent = max($iosPercent, $androidPercent);

        return [
            'period' => $period,
            'total' => $totalUsers,
            'ios' => [
                'name' => 'Apple iOS',
                'short_name' => 'iOS',
                'users' => $iosUsers,
                'percent' => $iosPercent,
                'is_top' => $topPlatform === 'iOS',
            ],
            'android' => [
                'name' => 'Google Android',
                'short_name' => 'Android',
                'users' => $androidUsers,
                'percent' => $androidPercent,
                'is_top' => $topPlatform === 'Android',
            ],
            'top_platform' => $topPlatform,
            'top_percent' => $topPercent,
            'has_data' => $totalUsers > 0,
        ];
    }

    /**
     * Dữ liệu dự phòng từ database user_devices khi không có mạng Firebase
     */
    protected function getFallbackDeviceOsStats(): array
    {
        $iosCount = 0;
        $androidCount = 0;

        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('user_devices')) {
                $devices = \Illuminate\Support\Facades\DB::table('user_devices')
                    ->where('is_active', true)
                    ->pluck('device_name');

                foreach ($devices as $deviceName) {
                    $lower = strtolower($deviceName ?? '');
                    if (str_contains($lower, 'iphone') || str_contains($lower, 'ipad') || str_contains($lower, 'ios') || str_contains($lower, 'apple')) {
                        $iosCount++;
                    } else {
                        $androidCount++;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('Fallback user_devices stats failed: ' . $e->getMessage());
        }

        $total = $iosCount + $androidCount;
        $effectiveTotal = $total > 0 ? $total : 1;
        $iosPercent = round(($iosCount / $effectiveTotal) * 100, 1);
        $androidPercent = round(100 - $iosPercent, 1);
        $topPlatform = $iosCount >= $androidCount ? 'iOS' : 'Android';

        return [
            'period' => 'all',
            'total' => $total,
            'ios' => [
                'name' => 'Apple iOS',
                'short_name' => 'iOS',
                'users' => $iosCount,
                'percent' => $iosPercent,
                'is_top' => $topPlatform === 'iOS',
            ],
            'android' => [
                'name' => 'Google Android',
                'short_name' => 'Android',
                'users' => $androidCount,
                'percent' => $androidPercent,
                'is_top' => $topPlatform === 'Android',
            ],
            'top_platform' => $topPlatform,
            'top_percent' => max($iosPercent, $androidPercent),
            'has_data' => $total > 0,
            'is_fallback' => true,
        ];
    }

    /**
     * Elegant Mock data for Version distributions.
     */
    protected function getMockActiveUsersByVersion(): array
    {
        return [
            ['version' => '1.2.0', 'users' => 195],
            ['version' => '1.1.1', 'users' => 12],
            ['version' => '1.0.3', 'users' => 3],
        ];
    }
}
