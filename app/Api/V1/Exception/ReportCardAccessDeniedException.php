<?php

namespace App\Api\V1\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ReportCardAccessDeniedException extends HttpException
{
    public function __construct($message = 'Bạn không có quyền truy cập học bạ này.',
                                $code = 403, \Throwable $previous = null,
                                array $headers = [], $statusCode = 403)
    {
        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }
}
