<?php

namespace TMPHP\RestApiGenerators\Compilers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Log;
use TMPHP\RestApiGenerators\AbstractEntities\StubCompilerAbstract;

class CrudTransformerCompiler extends StubCompilerAbstract
{

    /**
     * @var string
     */
    private $transformersNamespace;

    /**
     * @var string
     */
    private $modelsNamespace;

    /**
     * CrudTransformerCompiler constructor.
     * @param null $saveToPath
     * @param null $saveFileName
     * @param null $stub
     */
    public function __construct($saveToPath = null, $saveFileName = null, $stub = null)
    {
        $saveToPath = base_path(config('rest-api-generator.paths.transformers'));
        $saveFileName = '';

        $this->transformersNamespace = config('rest-api-generator.namespaces.transformers');
        $this->modelsNamespace = config('rest-api-generator.namespaces.models');

        parent::__construct($saveToPath, $saveFileName, $stub);
    }

    /**
     * @param array $params
     * @return bool|mixed|string
     */
    public function compile(array $params): string
    {
        //
        $this->saveFileName = ucfirst($params['modelNameCamelcase']) . 'Transformer.php';

        //modelNameCamelcase
        $this->stub = str_replace(
            '{{Model}}',
            ucfirst($params['modelNameCamelcase']),
            $this->stub
        );

        //transformersNamespace
        $this->stub = str_replace(
            '{{transformersNamespace}}',
            $this->transformersNamespace,
            $this->stub
        );

        $availableIncludesArray = new ArrayCompiler();
        $arrayValues = $this->getModelRelations($params['modelNameCamelcase']);
        $availableIncludesArrayCompiled = $availableIncludesArray->compile([
            'keys' => [],
            'values' => $arrayValues,
            'comment' => '',
            'name' => 'availableIncludes',
        ]);

        //{{modelRelations}}
        $this->stub = str_replace(
            '{{AvailableIncludesArray}}',
            $availableIncludesArrayCompiled,
            $this->stub
        );


        //
        $this->saveStub();

        //
        return $this->stub;
    }

    /**
     * TODO
     * @param string $modelName
     * @return array
     */
    private function getModelRelations(string $modelName): array
    {
        $relations = [];

        $modelFullClassName = $this->modelsNamespace. '\\'. studly_case($modelName);

        $model = new $modelFullClassName();

        $reflectionClass = new \ReflectionClass($modelFullClassName);

        //get methods, which are declared in model class
        $methods = [];
        foreach ($reflectionClass->getMethods() as $method) {
            if ($method->class == $modelFullClassName) {
                $methods[] = $method->name;
            }
        }

        //check methods, whether they are relations and add their names if yes
        foreach ($methods as $method){
            $methodResult = $model->$method();

            if ($methodResult instanceof Relation){
                $relations[] = "'$method'";
            }
        }

        return $relations;
    }


}