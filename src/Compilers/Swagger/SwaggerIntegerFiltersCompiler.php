<?php

namespace TMPHP\RestApiGenerators\Compilers\Swagger;

use Illuminate\Database\Eloquent\Model;
use TMPHP\RestApiGenerators\AbstractEntities\StubCompilerAbstract;

/**
 * Class SwaggerIntegerFiltersCompiler
 * @package TMPHP\RestApiGenerators\Compilers\Swagger
 */
class SwaggerIntegerFiltersCompiler extends StubCompilerAbstract
{

    /**
     * SwaggerIntegerFiltersCompiler constructor.
     *
     * @param null $saveToPath
     * @param null $saveFileName
     * @param null $stub
     */
    public function __construct($saveToPath = null, $saveFileName = null, $stub = null)
    {
        $saveToPath = base_path(config('rest-api-generator.paths.documentations'));
        $saveFileName = '';

        parent::__construct($saveToPath, $saveFileName, $stub);
    }

    /**
     * @param array $params
     * @return bool|mixed|string
     */
    public function compile(array $params): string
    {
        //
        $this->replaceInStub([
            '{{columnNameStudlyCase}}' => $params['columnNameStudlyCase'],
        ]);

        //
        return $this->stub;
    }

}