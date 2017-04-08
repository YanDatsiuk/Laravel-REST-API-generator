<?php

namespace TMPHP\RestApiGenerators\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Validator;

/**
 * Contract for realization return formatted errors
 *
 * Interface ErrorFormatable
 *
 * @package App\Contracts
 */
interface ErrorFormatable
{
    public function responseWithValidatorErrors(Validator $validator);

    public function responseNotFoundModel(Model $model = null, int $status_code = 422);

    public function responseCouldNotCreate(Model $model = null);

    public function responseErrorMessage(string $message, int $status_code, array $errors = []);
}