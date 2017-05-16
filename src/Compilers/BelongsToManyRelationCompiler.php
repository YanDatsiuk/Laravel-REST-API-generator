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
    public function compile(array $params): string
    {
        //
        $this->replaceInStub([
            '{{relatedModelStudlyCasePlural}}' => $params['relatedModelStudlyCasePlural'],
            '{{relatedModelCamelCasePlural}}' => $params['relatedModelCamelCasePlural'],
            '{{relatedModelCamelCaseSingular}}' => $params['relatedModelCamelCaseSingular'],
            '{{pivotTableName}}' => $params['pivotTableName'],
            '{{modelsNamespace}}' => $params['modelsNamespace']
        ]);

        //
        return $this->stub;
    }

}