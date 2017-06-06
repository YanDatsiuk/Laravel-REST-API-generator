<?php

namespace TMPHP\RestApiGenerators\Compilers\Routes;


use Illuminate\Database\Eloquent\Model;
use TMPHP\RestApiGenerators\AbstractEntities\StubCompilerAbstract;
use TMPHP\RestApiGenerators\Compilers\Swagger\SwaggerIntegerFiltersCompiler;
use TMPHP\RestApiGenerators\Support\Helper;
use TMPHP\RestApiGenerators\Support\SchemaManager;

/**
 * Class CrudModelRoutesCompiler
 * @property  model
 * @package TMPHP\RestApiGenerators\Compilers
 */
class CrudModelRoutesCompiler extends StubCompilerAbstract
{

    /**
     * @var Model
     */
    private $model;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @var SchemaManager
     */
    private $schema;

    /**
     * @var string
     */
    private $controllersNamespace;

    /**
     * @var string
     */
    private $modelsNamespace;

    /**
     * CrudModelRoutesCompiler constructor.
     *
     * @param null $saveToPath
     * @param null $saveFileName
     * @param null $stub
     */
    public function __construct($saveToPath = null, $saveFileName = null, $stub = null)
    {
        $saveToPath = base_path(config('rest-api-generator.paths.routes'));
        $saveFileName = '';

        $this->schema = new SchemaManager();
        $this->controllersNamespace = config('rest-api-generator.namespaces.controllers');
        $this->modelsNamespace = config('rest-api-generator.namespaces.models');

        parent::__construct($saveToPath, $saveFileName, $stub);
    }

    /**
     * @param array $params
     * @return bool|mixed|string
     */
    public function compile(array $params): string
    {

        //
        $modelSingularLowercase = $params['modelName'];

        //
        $modelPlurarLowercase = Helper::pluralizeKebabCase($params['modelName']);

        //
        $modelSingularUppercase = '';
        foreach (explode('-', $params['modelName']) as $pivotModelNamePart) {
            $modelSingularUppercase .= ucfirst($pivotModelNamePart);
        }

        //
        $modelFullClassName = $this->modelsNamespace . '\\' . studly_case($modelSingularUppercase);
        $this->model = new $modelFullClassName();

        $this->tableName = $this->model->getTable();

        //compile swagger filters for index method
        $compiledFilters = $this->compileSwaggerFilters();

        //
        $this->replaceInStub([
            '{{ModelSingularLowercase}}' => $modelSingularLowercase,
            '{{ModelPlurarLowercase}}' => $modelPlurarLowercase,
            '{{ModelSingularUppercase}}' => $modelSingularUppercase,
            '{{controllersNamespace}}' => $this->controllersNamespace,
            '{{filters}}' => $compiledFilters,
        ]);

        //
        return $this->stub;
    }


    /**
     * //todo realize
     *
     * @return string
     */
    private function compileSwaggerFilters()
    {
        $compiledFilters = '';

        /** @var \Doctrine\DBAL\Schema\Column[] $columns local table columns */
        $columns = $this->schema->listTableColumns($this->tableName);

        //compile scope for each local column
        foreach ($columns as $column) {
            $type = $column->getType();
            if ($type == 'Integer' || $type == 'SmallInt' || $type == 'BigInt') {
                $swaggerIntegerFilters = new SwaggerIntegerFiltersCompiler();
                $compiledFilters .= $swaggerIntegerFilters->compile([
                    'columnNameStudlyCase' => studly_case($column->getName())
                ]);
            }

            if ($type == 'Float' || $type == 'Decimal') {
                //todo add float filters compilation
            }

            if ($type == 'String') {
                //todo add string filters compilation
            }
        }

        return $compiledFilters;
    }


}