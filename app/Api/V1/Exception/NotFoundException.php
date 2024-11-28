<?php

namespace App\Api\V1\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class NotFoundException extends HttpException
{
    public function __construct($message = 'The requested resource was not found.',
                                $code = 404, \Throwable $previous = null,
                                array $headers = [], $statusCode = 404)
    {
        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }
}
