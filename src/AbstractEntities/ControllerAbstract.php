<?php

namespace TMPHP\RestApiGenerators\AbstractEntities;

use Dingo\Api\Http\Request;
use Dingo\Api\Routing\Helpers;
use Illuminate\Database\Eloquent\Builder as IlluminateQueryBuilder;
use Illuminate\Database\Eloquent\Model as IlluminateModel;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as IlluminateController;
use League\Fractal\TransformerAbstract;
use TMPHP\RestApiGenerators\Exceptions\WrongTypeException;
use TMPHP\RestApiGenerators\Helpers\Traits\ErrorFormatable;

/**
 * Base Controller contained most uses methods and vars
 * Realization basic logic for CRUD operations
 *
 * Class ControllerAbstract
 *
 * @package TMPHP\RestApiGenerators\AbstractEntities
 */
abstract class ControllerAbstract extends IlluminateController
{
    use Helpers,
        ErrorFormatable,
        AuthorizesRequests,
        DispatchesJobs,
        ValidatesRequests;

    /**
     * Container for current Model
     *
     * @var IlluminateModel $model
     */
    protected $model;

    /**
     * Container for builder
     *
     * @var IlluminateQueryBuilder
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
     * Validation rules
     *
     * @var array $rules
     */
    protected $rules = [
        'index' => [],
        'store' => [],
        'update' => [],
        'show' => [],
        'destroy' => [],
    ];

    /**
     * ControllerAbstract constructor
     *
     * @param IlluminateModel $model
     *
     * @param string $transformerClass namespace of transformer class
     */
    public function __construct(IlluminateModel $model, string $transformerClass)
    {
        $this->model = $model;
        $this->query = $model->query();
        $this->transformerClass = $transformerClass;
    }

    /**
     * Set Model
     *
     * @param IlluminateModel $model
     *
     * @return $this
     */
    public function setModel(IlluminateModel $model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Set Model
     *
     * @param IlluminateQueryBuilder $query
     *
     * @return $this
     */
    public function setQuery(IlluminateQueryBuilder $query)
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
        if (class_exists($transformer) && new $transformer() instanceof TransformerAbstract) {
            $this->transformerClass = $transformer;
        } else {
            throw new WrongTypeException(
                'Expected string of namespace to TransformerClass, but according this namespace class don\'t exist or have WrongType',
                500
            );
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
        $this->validate($request, $this->rules[__FUNCTION__] ?: []);
        $this->setParams($request);

        $paginator = $this->query->with($this->relations)->paginate($this->limit);

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
            $this->setLimit($request->input('limit'));
        }

        if ($request->input('page')) {
            $this->setPage($request->input('page'));
        }

        if ($request->input('include')) {
            $this->setRelations($request->input('include'));
        }
    }

    /**
     * Set requested limit\per page
     *
     * @param int|string $limit
     *
     * @return $this
     */
    protected function setLimit($limit)
    {
        $this->limit = intval($limit);

        return $this;
    }

    /**
     * Set requested page
     *
     * @param int|string $page
     *
     * @return $this
     */
    protected function setPage($page)
    {
        $this->page = intval($page);

        return $this;
    }

    /**
     * Set requested relations
     *
     * @param array $relations
     *
     * @return $this
     */
    public function setRelations($relations)
    {
        $this->relations = (array)$relations;

        return $this;
    }

    /**
     * Create new model item
     *
     * @param Request $request
     *
     * @return \Dingo\Api\Http\Response
     */
    public function store(Request $request)
    {
        $requestInputs = $request->all();

        $this->validate($request, $this->rules[__FUNCTION__] ?: []);

        $model = $this->model->newInstance();
        $model->fill($requestInputs)->save();

        if (!$model) {
            return $this->responseCouldNotCreate(get_class($this->model));
        }

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
     *
     * @return \Dingo\Api\Http\Response
     */
    public function show(Request $request, $id)
    {
        $this->validate($request, $this->rules[__FUNCTION__] ?: []);
        $this->setParams($request);

        $model = $this->query->find($id)->with($this->relations);//todo BUG if model === null;

        if (!$model) {
            return $this->responseNotFoundModel($model);
        }

        return $this->response->item(
            $model,
            new $this->transformerClass()
        );
    }

    /**
     * Update model by Id
     *
     * @param Request $request
     * @param $id
     *
     * @return \Dingo\Api\Http\Response|
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, $this->rules[__FUNCTION__] ?: []);
        $model = $this->query->withoutGlobalScopes()->find($id);

        if (!$model) {
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
     * @param $request
     *
     * @return \Dingo\Api\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $this->validate($request, $this->rules[__FUNCTION__] ?: []);
        $model = $this->query->withoutGlobalScopes()->find($id);

        if ($model === null) {
            return $this->responseNotFoundModel($this->model);
        }

        $model->delete();

        return $this->response->accepted();
    }
}