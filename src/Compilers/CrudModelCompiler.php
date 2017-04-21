<?php

namespace TMPHP\RestApiGenerators\Compilers;


use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Illuminate\Support\Facades\DB;
use TMPHP\RestApiGenerators\AbstractEntities\StubCompilerAbstract;

class CrudModelCompiler extends StubCompilerAbstract
{

    /**
     * @var AbstractSchemaManager
     */
    private $schema;

    /**
     * @var string
     */
    private $modelsNamespace;

    /**
     * CrudModelCompiler constructor.
     * @param null $saveToPath
     * @param null $saveFileName
     * @param null $stub
     */
    public function __construct($saveToPath = null, $saveFileName = null, $stub = null)
    {
        $saveToPath = base_path(config('rest-api-generator.paths.models'));
        $saveFileName = '';
        $this->schema = DB::getDoctrineSchemaManager();

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
        $this->saveFileName = ucfirst($params['modelName']) . '.php';

        /**
         * @var \Doctrine\DBAL\Schema\Column[]
         */
        $columns = $this->schema->listTableColumns($params['tableName']);

        //{{FillableArray}}
        $this->compileFillableArray($columns);

        //{{RulesArray}}
        $this->compileRulesArray($columns);

        //{{BelongsToRelations}}
        $this->compileBelongsToRelations($params['modelName'], $params['tableName']);

        //{{HasManyRelations}}
        $this->compileHasManyRelations($params['modelName'], $params['tableName']);

        //{{BelongsToManyRelations}}
        $this->compileBelongsToManyRelations($params['modelName'], $params['tableName']);


        //{{ModelCapitalized}}
        $this->stub = str_replace(
            '{{ModelCapitalized}}',
            ucfirst($params['modelName']),
            $this->stub
        );

        //{{table_name}}
        $this->stub = str_replace(
            '{{table_name}}',
            $params['tableName'],
            $this->stub
        );

        //{{modelsNamespace}}
        $this->stub = str_replace(
            '{{modelsNamespace}}',
            $this->modelsNamespace,
            $this->stub
        );

        //
        $this->saveStub();

        //
        return $this->stub;
    }

    /**
     * @param array $columns
     */
    private function compileFillableArray(array $columns)
    {
        $fillableArrayCompiler = new FillableArrayCompiler();
        $fillableArrayCompiled = $fillableArrayCompiler->compile(['columns' => $columns]);

        $this->stub = str_replace(
            '{{FillableArray}}',
            $fillableArrayCompiled,
            $this->stub
        );
    }

    /**
     * @param array $columns
     */
    private function compileRulesArray(array $columns)
    {
        $rulesArrayCompiler = new RulesArrayCompiler();
        $rulesArrayCompiled = $rulesArrayCompiler->compile(['columns' => $columns]);

        $this->stub = str_replace(
            '{{RulesArray}}',
            $rulesArrayCompiled,
            $this->stub
        );
    }

    /**
     * @param string $modelName
     * @param string $tableName
     */
    private function compileBelongsToRelations(string $modelName, string $tableName)
    {
        //todo get relations and call compiler for each
        $relationCompiler = new BelongsToRelationCompiler();
        $relationsCompiled = $relationCompiler->compile([
            'modelName' => $modelName,
            'tableName' => $tableName
        ]);

        $this->stub = str_replace(
            '{{BelongsToRelations}}',
            $relationsCompiled,
            $this->stub
        );
    }

    /**
     * @param string $modelName
     * @param string $tableName
     */
    private function compileHasManyRelations(string $modelName, string $tableName)
    {
        //todo get relations and call compiler for each
        $relationCompiler = new HasManyRelationCompiler();
        $relationsCompiled = $relationCompiler->compile([
            'modelName' => $modelName,
            'tableName' => $tableName
        ]);

        $this->stub = str_replace(
            '{{HasManyRelations}}',
            $relationsCompiled,
            $this->stub
        );
    }

    /**
     * @param string $modelName
     * @param string $tableName
     */
    private function compileBelongsToManyRelations(string $modelName, string $tableName)
    {
        //todo get relations and call compiler for each
        $relationCompiler = new BelongsToManyRelationCompiler();
        $relationsCompiled = $relationCompiler->compile([
            'modelName' => $modelName,
            'tableName' => $tableName
        ]);

        $this->stub = str_replace(
            '{{BelongsToManyRelations}}',
            $relationsCompiled,
            $this->stub
        );
    }


}