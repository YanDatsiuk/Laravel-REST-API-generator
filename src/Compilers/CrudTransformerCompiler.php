<?php

namespace TMPHP\RestApiGenerators\Compilers;

use TMPHP\RestApiGenerators\Core\StubCompilerAbstract;

class CrudTransformerCompiler extends StubCompilerAbstract
{

    /**
     * @var string
     */
    private $transformersNamespace;

    /**
     * CrudTransformerCompiler constructor.
     * @param null $saveToPath
     * @param null $saveFileName
     * @param null $stub
     */
    public function __construct($saveToPath = null, $saveFileName = null, $stub = null)
    {
        $saveToPath = storage_path('CRUD/Transformers/');
        $saveFileName = '';

        $this->transformersNamespace = config('rest-api-generator.transformers-namespace');

        parent::__construct($saveToPath, $saveFileName, $stub);
    }

    /**
     * @param array $params
     * @return bool|mixed|string
     */
    public function compile(array $params):string
    {
        //
        $this->saveFileName = ucfirst($params['modelNameCamelcase']).'Transformer.php';

        //
        $this->stub = str_replace(
            '{{Model}}',
            ucfirst($params['modelNameCamelcase']),
            $this->stub
        );

        //
        $this->stub = str_replace(
            '{{transformersNamespace}}',
            $this->transformersNamespace,
            $this->stub
        );

        //
        $this->saveStub();

        //
        return $this->stub;
    }

}