<?php

namespace App\Api\V1\Support;

use App\Admin\Repositories\Otp\OtpRepositoryInterface;
use App\Api\V1\Exception\BadRequestException;
use App\Mail\OtpMail;
use Exception;
use Ichtrojan\Otp\Otp;
use Illuminate\Support\Facades\Mail;


trait OTPEmail
{

    /**
     * @throws Exception
     */
    public function generateAndSendOtp(string $email, int $validity)
    {
        $otp = new Otp();

        $otpResponse = $otp->generate($email, 'numeric', 6, $validity);

        if ($otpResponse->status) {
            $otpCode = $otpResponse->token;

            Mail::to($email)->send(new OtpMail($otpCode));
            return $otpResponse;
        }

        return false;
    }

    public function validateOtpCode(string $email, string $otpCode): void
    {
        $otp = new Otp();
        $otpValidated = $otp->validate($email, $otpCode);

        if (!$otpValidated->status) {
            throw new BadRequestException('Invalid OTP');
        }
    }

    /**
     * @throws Exception
     */
    public function deleteOtpWithEmail(string $email): void
    {
        $otpRepository = app(OtpRepositoryInterface::class);
        $otps = $otpRepository->getBy(['identifier' => $email]);
        foreach ($otps as $otp) {
            $this->otpRepository->delete($otp->id);
        }
    }
}