<?php

namespace TMPHP\RestApiGenerators\Helpers\Traits;

use Dingo\Api\Http\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Model as IlluminateModel;

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
     * @param Model $model
     * @param integer $status_code
     *
     * @return Response
     */
    public function responseNotFoundModel(IlluminateModel $model, int $status_code = 422)
    {
        $this->message     = 'Not found this'.get_class($model);
        $this->errors      = [$model->getKey() => 'Not found this'.get_class($model)];
        $this->status_code = $status_code;

        return $this->response($this->formatErrors(), $this->status_code);
    }

    /** Format and set response with error */
    public function formatErrors()
    {
        return [
            'message'     => $this->message,
            'errors'      => $this->errors,
            'status_code' => $this->status_code,
        ];
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
     * @param string $nameOfEntity
     *
     * @return Response
     */
    public function responseCouldNotCreate($nameOfEntity)
    {
        $this->message     = 'Could not create'.$nameOfEntity;
        $this->errors[]    = '';
        $this->status_code = 500;

        return $this->response($this->formatErrors(), $this->status_code);
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
     *
     * @return Response
     */
    public function responseErrorMessage(string $message, int $status_code = 500, array $errors = [])
    {
        $this->message     = $message;
        $this->errors      = $errors;
        $this->status_code = $status_code;

        return $this->response($this->formatErrors(), $this->status_code);
    }

    /**
     * Response.
     * You can overwrite this method for customization response object or logic
     *
     * @param $content
     * @param int $status_code
     * @param array $headers
     *
     * @return Response
     */
    public function response($content, $status_code = 200, array $headers = []): \Symfony\Component\HttpFoundation\Response
    {
        return new Response($content, $status_code, $headers);
    }
}