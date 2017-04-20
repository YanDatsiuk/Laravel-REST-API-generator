<?php

namespace TMPHP\RestApiGenerators\Compilers;

use TMPHP\RestApiGenerators\AbstractEntities\StubCompilerAbstract;

/**
 * Class CrudControllerCompiler
 * @package TMPHP\RestApiGenerators\Compilers
 */
class CrudControllerCompiler extends StubCompilerAbstract
{
    /** @var string $controllersNamespace */
    private $controllersNamespace;

    /** @var string $modelsNamespace */
    private $modelsNamespace;

    /** @var string $transformersNamespace*/
    private $transformersNamespace;

    /**
     * CrudControllerCompiler constructor.
     *
     * @param null $saveToPath
     * @param null $saveFileName
     * @param null $stub
     */
    public function __construct($saveToPath = null, $saveFileName = null, $stub = null)
    {
        $saveToPath   = config('rest-api-generator.paths.controllers');
        $saveFileName = '';

        $this->controllersNamespace  = config('rest-api-generator.namespaces.controllers');
        $this->modelsNamespace       = config('rest-api-generator.namespaces.models');
        $this->transformersNamespace = config('rest-api-generator.namespaces.transformers');

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
        $this->saveFileName = ucfirst($params['modelNameCamelcase']).'Controller.php';

        $this->stub = str_replace(
            '{{Model}}',
            ucfirst($params['modelNameCamelcase']),
            $this->stub
        );

        $this->stub = str_replace(
            '{{controllersNamespace}}',
            $this->controllersNamespace,
            $this->stub
        );

        $this->stub = str_replace(
            '{{modelsNamespace}}',
            $this->modelsNamespace,
            $this->stub
        );

        $this->stub = str_replace(
            '{{transformersNamespace}}',
            $this->transformersNamespace,
            $this->stub
        );

        $this->saveStub();

        return $this->stub;
    }
}