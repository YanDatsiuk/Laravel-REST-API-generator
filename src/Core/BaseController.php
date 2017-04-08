<?php

namespace TMPHP\RestApiGenerators\Core;

use TMPHP\RestApiGenerators\Contracts\Validable as ValidableContract;
use TMPHP\RestApiGenerators\Helpers\ErrorFormatable;
use TMPHP\RestApiGenerators\Helpers\Validable;
use App\Exceptions\WrongTypeException;
use App\Http\Controllers\Controller;
use Dingo\Api\Http\Request;
use Dingo\Api\Routing\Helpers;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use League\Fractal\TransformerAbstract;

/**
 * Base Controller contained most uses methods and vars
 * Realization basic logic for CRUD operations
 *
 * Class BaseController
 *
 * @package App\Core
 */
abstract class BaseController extends Controller implements ValidableContract
{
    use Helpers, Validable, ErrorFormatable;

    /**
     * Container for current Model
     *
     * @var BaseModel $model
     */
    protected $model;

    /**
     * Container for builder
     *
     * @var Builder
     */
    protected $query;

    /**
     * Container for current Transformer
     *
     * @var string $transformer
     */
    protected $transformerClass;

    /**
     * Count of objects per page
     *
     * @var int $limit
     */
    protected $limit = 10;

    /**
     * Number of needed page
     *
     * @var int
     */
    protected $page = 1;

    /**
     * Requested relations
     *
     * @var array $relations
     */
    protected $relations = [];

    /**
     * BaseController constructor
     *
     * @param BaseModel $model
     *
     * @param string $transformerClass namespace of transformer class
     */
    public function __construct(BaseModel $model, string $transformerClass)
    {
        $this->model = $model;
        $this->query = $model->query();
        $this->transformerClass = $transformerClass;
    }

    /**
     * Set Model
     *
     * @param BaseModel $model
     *
     * @return $this
     */
    public function setModel(BaseModel $model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Set Model
     *
     * @param Builder $query
     * @return $this
     */
    public function setQuery(Builder $query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Set transformer class
     *
     * @param string $transformer
     *
     * @return $this
     * @throws WrongTypeException
     */
    public function setTransformClass(string $transformer)
    {
        if (class_exists($transformer) && new $transformer([]) instanceof TransformerAbstract) {
            $this->transformerClass = $transformer;
        } else {
            throw new WrongTypeException('Expected string of namespace to TransformerClass, but according this namespace class don\'t exist or have WrongType',
                500);
        }

        return $this;
    }

    /**
     * Get items list of model
     *
     * @param Request $request
     *
     * @return \Dingo\Api\Http\Response
     */
    public function index(Request $request)
    {
        $requestInputs = $request->all();

        //validating data from request
        $validator = $this->model->checkRules($requestInputs);

        if ($validator->fails()) {
            return $this->responseWithValidatorErrors($validator);
        }

        //setting common parameters
        $this->setParams($request);

        //getting response from cache or setting it into cache
        /*$paginator = SmartCache::paginateByQuery(
            [get_class($this->model)],
            $this->query->with($this->model->getRelationNames()),
            $this->model->getKeys(),
            $this->page,
            $this->limit);*/

        $paginator = $this->query->paginate();

        return $this->response->paginator($paginator, new $this->transformerClass());
    }

    /**
     * Set limit,page,keys,relations, params from Request
     * and set keys and relations to model
     *
     * @param Request $request
     */
    protected function setParams(Request $request)
    {
        if ($request->input('limit')) {
            $this->setRequestedLimit($request->input('limit'));
        }

        if ($request->input('page')) {
            $this->setRequestedPage($request->input('page'));
        }
    }

    /**
     * Set requested limit\per page
     *
     * @param int|string $limit
     * @return $this
     */
    protected function setRequestedLimit($limit)
    {
        $this->limit = intval($limit);

        return $this;
    }

    /**
     * Set requested page
     *
     * @param int|string $page
     * @return $this
     */
    protected function setRequestedPage($page)
    {
        $this->page = intval($page);

        return $this;
    }

    /**
     * Create new model item
     *
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     */
    public function store(Request $request)
    {
        $requestInputs = $request->all();

        //validating data from request
        $validator = $this->model->checkRules($requestInputs, false);

        if ($validator->fails()) {
            return $this->responseWithValidatorErrors($validator);
        }

        //setting common parameters
        $this->setParams($request);

        $model = $this->model->newInstance();
        $model->fill($requestInputs)->save();

        if (!$model->getKey()) {
            return $this->responseCouldNotCreate(get_class($this->model));
        }

        //response
        return $this->response->item(
            $model,
            new $this->transformerClass()
        );
    }

    /**
     * Get specific model item
     *
     * @param Request $request
     * @param $id
     * @return \Dingo\Api\Http\Response|Response
     */
    public function show(Request $request, $id)
    {
        //setting common parameters
        $this->setParams($request);

        $requestInputs = $request->all();

        //validating data from request
        $validator = $this->model->checkRules($requestInputs);

        if ($validator->fails()) {
            return $this->responseWithValidatorErrors($validator);
        }

        $data = $this->query->where('id', '=', $id)->first();

        //response
        return $this->response->item(
            $data,
            new $this->transformerClass()
        );
    }

    /**
     * Update model by Id
     *
     * @param Request $request
     * @param $id
     * @return \Dingo\Api\Http\Response|
     */
    public function update(Request $request, $id)
    {
        $requestInputs = $request->all();

        //validating data from request
        $validator = $this->model->checkRules($requestInputs);

        if ($validator->fails()) {
            return $this->responseWithValidatorErrors($validator);
        }

        //setting common parameters
        $this->setParams($request);

        $model = $this->query->withoutGlobalScopes()->find($id);

        if ($model === null) {
            return $this->responseNotFoundModel($this->model);
        }

        $model->fill($request->all())->update();

        return $this->response->item(
            $model,
            new $this->transformerClass()
        );
    }

    /**
     * Remove from DB by id
     *
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function destroy($id)
    {
        $model = $this->query->withoutGlobalScopes()->find($id);

        if ($model === null) {
            return $this->responseNotFoundModel($this->model);
        }

        $model->delete();

        return $this->response->accepted();
    }

    /**
     * Validates request inputs by action rules
     *
     * @param $requestInputs
     * @param $actionName
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validateRequest(array $requestInputs, string $actionName)
    {
        $rules = [];

        //get validation rules for action
        if (isset($this->rules[$actionName])) {
            $rules = $this->rules[$actionName];
        }

        return $this->checkRules($requestInputs, $rules);
    }

    /**
     * Overwrite logic of check rules
     * for validate requests
     *
     * @param array $requestInputs
     * @param array $rules
     * @return \Illuminate\Validation\Validator
     */
    public function checkRules(array $requestInputs, array $rules = []): \Illuminate\Validation\Validator
    {
        return Validator::make($requestInputs, $rules);
    }
}