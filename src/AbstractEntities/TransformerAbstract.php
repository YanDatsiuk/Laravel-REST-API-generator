<?php

namespace TMPHP\RestApiGenerators\AbstractEntities;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use League\Fractal\ParamBag;
use TMPHP\RestApiGenerators\Exceptions\UnexpectedMagicCall;

abstract class TransformerAbstract extends \League\Fractal\TransformerAbstract
{
    /**
     * @var array
     */
    protected $availableIncludes = [];

    /**
     * @var array
     */
    private $validParams = ['limit'];

    /**
     * Transform model data to array
     *
     * @param Model $model
     * @return array
     */
    public function transform(Model $model)
    {
        return $model->toArray();
    }

    /**
     * @param $name
     * @param $arguments
     * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\Item|null
     * @throws UnexpectedMagicCall
     */
    public function __call($name, $arguments)
    {
        if (starts_with($name, 'include')) {
            return $this->callInclude($name, $arguments);
        } else {
            throw new UnexpectedMagicCall();
        }
    }

    /**
     * @param $name
     * @param $arguments
     * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\Item|null
     * @throws \Exception
     */
    private function callInclude($name, $arguments)
    {
        //get requested relation name
        $relationName = str_replace_first('include', '', $name);

        //parse arguments
        $model = $arguments[0];
        $paramBag = $arguments[1];

        //
        $relation = $model->$relationName();

        $relatedModelClassName = class_basename($relation->getRelated());
        $transformerClassName = config('rest-api-generator.namespaces.transformers') . '\\' . $relatedModelClassName . 'Transformer';

        //calling proper include method, based on relation type
        switch (class_basename($relation)) {

            case 'HasMany':
                return $this->includeCollection($model, $relationName, $paramBag, $transformerClassName);
                break;

            case 'MorphMany':
                return $this->includeCollection($model, $relationName, $paramBag, $transformerClassName);
                break;

            case 'HasManyThrough':
                return $this->includeCollection($model, $relationName, $paramBag, $transformerClassName);
                break;

            case 'BelongsToMany':
                return $this->includeCollection($model, $relationName, $paramBag, $transformerClassName);
                break;

            case 'BelongsTo':
                return $this->includeItem($model, $relationName, $transformerClassName);
                break;

            case 'MorphTo':
                return $this->includeItem($model, $relationName, $transformerClassName);
                break;

            case 'MorphOne':
                return $this->includeItem($model, $relationName, $transformerClassName);
                break;

            case 'HasOne':
                return $this->includeItem($model, $relationName, $transformerClassName);
                break;

            default:
                throw new \Exception(class_basename($relation) . ' no behaviour specified in transformer!');
                break;
        }
    }

    /**
     * @param $model
     * @param string $relationName
     * @param string $transformerClassName
     * @return \League\Fractal\Resource\Item|null
     */
    protected function includeItem($model, string $relationName, string $transformerClassName)
    {
        $relatedData = $model->$relationName;

        if ($relatedData === null) {
            return null;
        }

        return $this->item($relatedData, new $transformerClassName);
    }

    /**
     * @param $model
     * @param string $relationName
     * @param ParamBag|null $params
     * @param string $transformerClassName
     * @return \League\Fractal\Resource\Collection
     * @throws \Exception
     */
    protected function includeCollection(
        $model,
        string $relationName,
        ParamBag $params = null,
        string $transformerClassName
    ) {
        if ($params === null) {
            return $model->$relationName;
        }

        //Optional params validation
        $usedParams = array_keys(iterator_to_array($params));
        if ($invalidParams = array_diff($usedParams, $this->validParams)) {
            throw new \Exception(sprintf(
                'Invalid param(s): "%s". Valid param(s): "%s"',
                implode(',', $usedParams),
                implode(',', $this->validParams)
            ));
        }

        //Processing limit parameter
        list($limit, $offset) = $params->get('limit');
        $limit = ($limit === null) ? 10 : $limit;
        $offset = ($offset === null) ? 0 : $offset;

        //list($orderCol, $orderBy) = $params->get('order');

        $relatedData = $model->$relationName()
            ->take($limit)
            ->skip($offset)
            //->orderBy($orderCol, $orderBy)
            ->get();

        return $this->collection($relatedData, new $transformerClassName);
    }
}