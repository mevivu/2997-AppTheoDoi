<?php

namespace App\Api\V1\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class BadRequestException extends HttpException
{
    public function __construct($message = null, \Throwable $previous = null, array $headers = [], $code = 0)
    {
        parent::__construct(400, $message ?: 'Bad request.', $previous, $headers, $code);
    }
}
