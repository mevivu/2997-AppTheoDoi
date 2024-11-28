<?php

namespace App\Api\V1\Validate;

use App\Api\V1\Exception\BadRequestException;
use App\Api\V1\Exception\NotFoundException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Validator
{
    public static function validateExists($repository, $id): bool
    {
        try {
            if (!is_numeric($id) || !ctype_digit(strval($id))) {
                throw new BadRequestException("ID {$id} is not a valid integer.");
            }
            $repository->findOrFail($id);
            return true;
        } catch (ModelNotFoundException $e) {
            throw new NotFoundException("Resource with ID {$id} not found.");
        }
    }

    public static function validateExistsByField($repository, $field, $value): bool
    {
        try {
            if (empty($value)) {
                throw new BadRequestException("The value for {$field} cannot be empty.");
            }
            $result = $repository->findByField($field, $value);
            if (empty($result)) {
                throw new NotFoundException("Resource with {$field} = {$value} not found.");
            }
            return true;
        } catch (ModelNotFoundException $e) {
            throw new NotFoundException("Resource with {$field} = {$value} not found.");
        }
    }
}
