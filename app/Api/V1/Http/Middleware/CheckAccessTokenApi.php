<?php

namespace App\Api\V1\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CheckAccessTokenApi
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return JsonResponse
     */
    public function handle(Request $request, Closure $next): JsonResponse
    {

        if ($this->isPurchaseWebhook($request)) {
            return $next($request);
        }

        if($request->header('X-TOKEN-ACCESS') === config('custom_api.X-TOKEN-ACCESS')){
            return $next($request);
        }

        return response()->json([
            'status' => 403,
            'message' => __('Bạn không có quyền truy cập.')
        ], 403);
    }

    /**
     * Kiểm tra xem có phải là purchase webhook không
     */
    private function isPurchaseWebhook(Request $request): bool
    {
        $path = $request->path();

        $purchasePatterns = [
            'api/v1/purchase/google-play/webhook',
            'api/v1/purchase/app-store/webhook',
            'api/v1/purchase/*/webhook',
        ];

        foreach ($purchasePatterns as $pattern) {
            if (fnmatch($pattern, $path)) {
                return true;
            }
        }

        return false;
    }
}
