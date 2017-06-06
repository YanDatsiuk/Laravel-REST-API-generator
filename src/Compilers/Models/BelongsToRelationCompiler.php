<?php

namespace TMPHP\RestApiGenerators\Compilers\Models;


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

        //
        $this->replaceInStub([
            '{{belongToRelationName}}' => $params['belongToRelationName'],
            '{{relatedModelStudlyCaseSingular}}' => studly_case($modelName),
            '{{modelsNamespace}}' => $params['modelsNamespace'],
        ]);

        //
        return $this->stub;
    }

}