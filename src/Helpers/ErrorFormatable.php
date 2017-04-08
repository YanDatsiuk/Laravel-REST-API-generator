<?php

namespace TMPHP\RestApiGenerators\Helpers;

use Dingo\Api\Http\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Validator;

/**
 * Realization return formatted errors
 * according ErrorFormatable contract
 *
 * Class ErrorFormatable
 *
 * @package App\Helpers
 */
trait ErrorFormatable
{
    /** @var string $message */
    protected $message = '';

    /** @var array $errors */
    protected $errors = [];

    /** @var int $status_code */
    protected $status_code = 0;

    /** @var array $errorResponse */
    protected $errorResponse = [];

    /**
     * Return response with errors according format.
     *
     * Format example:
     * {
     *      "message": "Unprocessable Entity",
     *      "errors": {
     *          "email": "The email format is invalid.",
     *          "birthday": "The birthday format is invalid."
     *       }
     *      "code": 422,
     *      "status_code": 422
     * }
     *
     * @param Validator $validator
     *
     * @return Response
     */
    public function responseWithValidatorErrors(Validator $validator)
    {
        $this->message = $validator->messages()->first();
        $this->errors = $validator->errors()->toArray();
        $this->status_code = 422;
        $this->formatErrorResponse();

        return new Response($this->errorResponse, $this->status_code);
    }

    /** Format and set response with error */
    private function formatErrorResponse()
    {
        $this->errorResponse = [
            'message' => $this->message,
            'errors' => $this->errors,
            'status_code' => $this->status_code
        ];

        return $this;
    }

    /**
     * Return response with errors according format.
     *
     * Format example:
     * {
     *      "message": "Unprocessable Entity",
     *      "errors": {
     *          "id": "Not found this $modelName",
     *       }
     *      "code": 422,
     *      "status_code": 422
     * }
     *
     * @param Model|null $model
     * @param integer $status_code
     *
     * @return Response
     */
    public function responseNotFoundModel(Model $model = null, int $status_code = 422)
    {
        $modelName = ($model !== null) ? get_class($model) : get_class($this->model);

        $this->message = 'Not found this' . $modelName;
        $this->errors = [$model->getKey() => 'Not found this' . $modelName];
        $this->status_code = $status_code;
        $this->formatErrorResponse();

        return new Response($this->errorResponse, $this->status_code);
    }

    /**
     * Return response with errors according format.
     *
     * Format example:
     * {
     *      "message": "Unprocessable Entity",
     *      "errors": {
     *          "id": "Could not create $modelName",
     *       }
     *      "status_code": 500
     * }
     *
     * @param Model|null $model
     * @return Response
     */
    public function responseCouldNotCreate(Model $model = null)
    {
        $modelName = ($model !== null) ? get_class($model) : get_class($this->model);

        $this->message = 'Could not create' . $modelName;
        $this->errors = [$model->getKey() => 'Could not create' . $modelName];
        $this->status_code = 500;
        $this->formatErrorResponse();

        return new Response($this->errorResponse, $this->status_code);
    }

    /**
     * Return response with errors according format.
     *
     * Format example:
     * {
     *      "message": {$message},
     *      "errors": {
     *          {$errors},
     *       }
     *      "status_code": {$status_code}
     * }
     *
     * @param string $message
     * @param int $status_code
     * @param array $errors
     * @return Response
     */
    public function responseErrorMessage(string $message, int $status_code = 500, array $errors = [])
    {
        $this->message = $message;
        $this->errors = $errors;
        $this->status_code = $status_code;
        $this->formatErrorResponse();

        return new Response($this->errorResponse, $this->status_code);
    }

}