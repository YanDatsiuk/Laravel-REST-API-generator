<?php

namespace TMPHP\RestApiGenerators\Compilers;


use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Illuminate\Support\Facades\DB;
use TMPHP\RestApiGenerators\Core\StubCompiler;

class CrudControllerCompiler extends StubCompiler
{

    /**
     * @var string
     */
    private $controllersNamespace;

    /**
     * @var string
     */
    private $modelsNamespace;

    /**
     * @var string
     */
    private $transformersNamespace;

    /**
     * CrudControllerCompiler constructor.
     * @param null $saveToPath
     * @param null $saveFileName
     * @param null $stub
     */
    public function __construct($saveToPath = null, $saveFileName = null, $stub = null)
    {
        $saveToPath = storage_path('CRUD/Controllers/');
        $saveFileName = '';

        $this->controllersNamespace = config('rest-api-generator.controllers-namespace');
        $this->modelsNamespace = config('rest-api-generator.models-namespace');
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
        $this->saveFileName = ucfirst($params['modelNameCamelcase']).'Controller.php';

        //
        $this->stub = str_replace(
            '{{Model}}',
            ucfirst($params['modelNameCamelcase']),
            $this->stub
        );

        //
        $this->stub = str_replace(
            '{{controllersNamespace}}',
            $this->controllersNamespace,
            $this->stub
        );

        //
        $this->stub = str_replace(
            '{{modelsNamespace}}',
            $this->modelsNamespace,
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