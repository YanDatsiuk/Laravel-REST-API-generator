<?php

namespace TMPHP\RestApiGenerators\Compilers\Scopes;

use TMPHP\RestApiGenerators\AbstractEntities\StubCompilerAbstract;

/**
 * Class WhereScopeCompiler
 * @package TMPHP\RestApiGenerators\Compilers\Scopes
 */
class WhereScopeCompiler extends StubCompilerAbstract
{

    /**
     * WhereScopeCompiler constructor.
     *
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
     *
     * @return string
     */
    public function compile(array $params): string
    {
        //todo

        return $this->stub;
    }
}