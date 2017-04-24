<?php

namespace TMPHP\RestApiGenerators\Compilers;


use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Illuminate\Support\Facades\Log;
use TMPHP\RestApiGenerators\AbstractEntities\StubCompilerAbstract;

class BelongsToRelationCompiler extends StubCompilerAbstract
{

    /**
     * BelongsToRelationCompiler constructor.
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

        $modelName = $params['relatedModelName'];

        //{{relatedModelCamelCaseSingular}}
        $this->stub = str_replace(
            '{{belongToRelationName}}',
            $params['belongToRelationName'],
            $this->stub
        );

        //{{relatedModelCamelCaseSingular}}
        $this->stub = str_replace(
            '{{relatedModelStudlyCaseSingular}}',
            studly_case($modelName),
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