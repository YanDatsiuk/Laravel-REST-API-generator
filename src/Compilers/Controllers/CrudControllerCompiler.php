<?php

namespace TMPHP\RestApiGenerators\Compilers\Controllers;

use Doctrine\DBAL\Schema\Column;
use Illuminate\Database\Eloquent\Model;
use TMPHP\RestApiGenerators\AbstractEntities\StubCompilerAbstract;
use TMPHP\RestApiGenerators\Compilers\Core\RulesArrayCompiler;
use TMPHP\RestApiGenerators\Support\SchemaManager;

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

    /** @var string $transformersNamespace */
    private $transformersNamespace;

    /** @var  string */
    private $tableName;

    /** @var  Model */
    private $model;

    /** @var Column[] */
    private $columns;

    /**
     * @var SchemaManager
     */
    private $schema;

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

        $this->schema = new SchemaManager();
        $this->controllersNamespace = config('rest-api-generator.namespaces.controllers');
        $this->modelsNamespace = config('rest-api-generator.namespaces.models');
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
        //
        $this->replaceInStub([
            '{{Model}}' => ucfirst($params['modelNameCamelcase']),
            '{{controllersNamespace}}' => $this->controllersNamespace,
            '{{modelsNamespace}}' => $this->modelsNamespace,
            '{{transformersNamespace}}' => $this->transformersNamespace,
        ]);

        $this->saveFileName = ucfirst($params['modelNameCamelcase']) . 'Controller.php';
        $modelClassWithNamespace = $this->modelsNamespace. '\\'. ucfirst($params['modelNameCamelcase']);
        $this->model = new $modelClassWithNamespace();
        $this->tableName = $this->model->getTable();
        $this->columns = $this->schema->listTableColumns($this->tableName);

        //{{RulesArray}}
        $this->compileRulesArray($this->columns, $this->tableName);

        $this->saveStub();

        return $this->stub;
    }

    /**
     * @param array $columns
     * @param string $tableName
     */
    private function compileRulesArray(array $columns, string $tableName)
    {
        $rulesArrayCompiler = new RulesArrayCompiler();
        $rulesArrayCompiled = $rulesArrayCompiler->compile([
            'columns' => $columns,
            'tableName' => $tableName,
        ]);

        //{{rulesArray}}
        $this->replaceInStub(['{{rulesArray}}' => $rulesArrayCompiled]);
    }
}