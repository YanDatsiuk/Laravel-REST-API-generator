<?php

namespace TMPHP\RestApiGenerators\Compilers;


use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Illuminate\Support\Facades\Log;
use TMPHP\RestApiGenerators\AbstractEntities\StubCompilerAbstract;

class BelongsToManyRelationCompiler extends StubCompilerAbstract
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

        //{{relatedModelStudlyCasePlural}}
        $this->stub = str_replace(
            '{{relatedModelStudlyCasePlural}}',
            $params['relatedModelStudlyCasePlural'],
            $this->stub
        );

        //{{relatedModelCamelCasePlural}}
        $this->stub = str_replace(
            '{{relatedModelCamelCasePlural}}',
            $params['relatedModelCamelCasePlural'],
            $this->stub
        );

        //{{relatedModelCamelCaseSingular}}
        $this->stub = str_replace(
            '{{relatedModelCamelCaseSingular}}',
            $params['relatedModelCamelCaseSingular'],
            $this->stub
        );

        //{{pivotTableName}}
        $this->stub = str_replace(
            '{{pivotTableName}}',
            $params['pivotTableName'],
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