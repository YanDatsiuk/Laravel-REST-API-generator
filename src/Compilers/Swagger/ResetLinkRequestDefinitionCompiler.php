<?php

namespace TMPHP\RestApiGenerators\Compilers\Swagger;


use TMPHP\RestApiGenerators\AbstractEntities\StubCompilerAbstract;

/**
 * Class ResetLinkRequestDefinitionCompiler
 * @package TMPHP\RestApiGenerators\Compilers
 */
class ResetLinkRequestDefinitionCompiler extends StubCompilerAbstract
{

    /**
     * ResetLinkRequestDefinitionCompiler constructor.
     * @param null $saveToPath
     * @param null $saveFileName
     * @param null $stub
     */
    public function __construct($saveToPath = null, $saveFileName = null, $stub = null)
    {
        $saveToPath = base_path(config('rest-api-generator.paths.documentations'));
        $saveFileName = 'reset-link-request.php';

        parent::__construct($saveToPath, $saveFileName, $stub);
    }

    /**
     * @param array $params
     * @return bool|mixed|string
     */
    public function compile(array $params): string
    {
        //
        $this->saveStub();

        //
        return $this->stub;
    }

}