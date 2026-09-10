<?php

namespace App\Admin\Services\User;
use Illuminate\Http\Request;

interface UserServiceInterface
{

    public function store(Request $request);

    public function update(Request $request);

    public function delete($id);

    public function actionMultipleRecode(Request $request);

    public function clearNormalTokens(): bool;

    public function revokeDevice(int $userId, int $deviceId): bool;

    public function revokeAllDevices(int $userId): bool;

    public function forceDelete($id): bool;
}

