<?php

namespace TMPHP\RestApiGenerators\Compilers;


use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Illuminate\Support\Facades\Log;
use TMPHP\RestApiGenerators\AbstractEntities\StubCompilerAbstract;

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
    public function compile(array $params):string
    {

        $modelName = $params['modelName'];

        //{{relatedModelCamelCasePlural}} //todo convert
        $this->stub = str_replace(
            '{{relatedModelCamelCasePlural}}',
            str_plural(camel_case($modelName)),
            $this->stub
        );

        //{{relatedModelStudlyCaseSingular}} //todo convert
        $this->stub = str_replace(
            '{{relatedModelStudlyCaseSingular}}',
            studly_case($modelName),
            $this->stub
        );

        //{{foreignKey}}
        $this->stub = str_replace(
            '{{foreignKey}}',
            $params['foreignKey'],
            $this->stub
        );

        //{{modelsNamespace}}
        $this->stub = str_replace(
            '{{modelsNamespace}}',
            $params['modelsNamespace'],
            $this->stub
        );

        //
        return $this->stub;
    }

}