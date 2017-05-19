<?php

namespace TMPHP\RestApiGenerators\Compilers;

use TMPHP\RestApiGenerators\AbstractEntities\StubCompilerAbstract;

/**
 * Class AuthControllerCompiler
 * @package TMPHP\RestApiGenerators\Compilers
 */
class AuthControllerCompiler extends StubCompilerAbstract
{

    /** @var string $controllersNamespace */
    private $controllersNamespace;

    /**
     * CrudControllerCompiler constructor.
     *
     * @param null $saveToPath
     * @param null $saveFileName
     * @param null $stub
     */
    public function __construct($saveToPath = null, $saveFileName = null, $stub = null)
    {
        $saveToPath = base_path(config('rest-api-generator.paths.controllers'));
        $saveFileName = '';

        $this->controllersNamespace = config('rest-api-generator.namespaces.controllers');

        parent::__construct($saveToPath, $saveFileName, $stub);
    }

    /**
     * Run compile process
     *
     * @param array $params
     *
     * @return bool|mixed|string
     */
    public function compile(array $params): string
    {
        $this->saveFileName = 'AuthController.php';

        //
        $this->replaceInStub([
            '{{controllersNamespace}}' => $this->controllersNamespace,
        ]);

        $this->saveStub();

        return $this->stub;
    }

}