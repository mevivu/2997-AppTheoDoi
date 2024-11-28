<?php

namespace App\Api\V1\Services\User;
use Illuminate\Http\Request;

interface UserServiceInterface
{

    public function store(Request $request);

    public function update(Request $request);

    public function updatePassword(Request $request);

    public function forgotPassword(Request $request);

    public function delete($id);

    public function validateOtp(Request $request);

    public function resendOtp(Request $request);


    public function updateEmail(Request $request): bool|object;

}
