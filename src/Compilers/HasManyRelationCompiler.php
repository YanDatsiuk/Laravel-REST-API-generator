<?php

namespace TMPHP\RestApiGenerators\Compilers;


use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Illuminate\Support\Facades\Log;
use TMPHP\RestApiGenerators\AbstractEntities\StubCompilerAbstract;

/**
 * Class HasManyRelationCompiler
 * @package TMPHP\RestApiGenerators\Compilers
 */
class HasManyRelationCompiler extends StubCompilerAbstract
{

    /**
     * HasManyRelationCompiler constructor.
     * @param null $saveToPath
     * @param null $saveFileName
     * @param null $stub
     */
    public function __construct($saveToPath = null, $saveFileName = null, $stub = null)
    {
        $saveToPath = base_path(config('rest-api-generator.paths.models'));
        $saveFileName = '';

        parent::__construct($saveToPath, $saveFileName, $stub);
    }

    /**
     * @param array $params
     * @return string
     */
    public function compile(array $params): string
    {
        $modelName = $params['modelName'];

        //
        $this->replaceInStub([
            '{{relatedModelCamelCasePlural}}' => str_plural(camel_case($modelName)),
            '{{relatedModelStudlyCaseSingular}}' => studly_case($modelName),
            '{{foreignKey}}' => $params['foreignKey'],
            '{{modelsNamespace}}' => $params['modelsNamespace'],
        ]);

        //
        return $this->stub;
    }

}