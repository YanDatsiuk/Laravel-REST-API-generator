<?php

namespace TMPHP\RestApiGenerators\Compilers;


use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use TMPHP\RestApiGenerators\AbstractEntities\StubCompilerAbstract;
use TMPHP\RestApiGenerators\Support\Helper;
use TMPHP\RestApiGenerators\Support\SchemaManager;

class CrudModelCompiler extends StubCompilerAbstract
{

    /**
     * @var SchemaManager
     */
    private $schema;

    /**
     * @var string
     */
    private $modelsNamespace;

    /**
     * @var string
     */
    private $dbTablePrefix;

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
        $this->schema = new SchemaManager();

        $this->modelsNamespace = config('rest-api-generator.namespaces.models');
        $this->dbTablePrefix = config('rest-api-generator.db_table_prefix');

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
        $this->compileBelongsToRelations($params['tableName']);

        //{{HasManyRelations}}
        $this->compileHasManyRelations($params['tableName']);

        //{{BelongsToManyRelations}}
        //$this->compileBelongsToManyRelations($params['tableName']);


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
     * @param string $tableName
     */
    private function compileBelongsToRelations(string $tableName)
    {
        /** @var  $foreignKeys \Doctrine\DBAL\Schema\ForeignKeyConstraint[] */
        $foreignKeys = $this->schema->listTableForeignKeys($tableName);

        $relationsCompiled = '';

        //get relations and call compiler for each
        foreach ($foreignKeys as $foreignKey) {

            $foreignTableName = $foreignKey->getForeignTableName();

            $relatedModelName = Helper::tableNameToModelName($foreignTableName, $this->dbTablePrefix);
            $belongToRelationName = Helper::columnNameToBelongToRelationName($foreignKey->getColumns()[0]);//todo

            $relationCompiler = new BelongsToRelationCompiler();
            $relationsCompiled .= $relationCompiler->compile([
                'relatedModelName' => $relatedModelName,
                'belongToRelationName' => $belongToRelationName,
                'modelsNamespace' => $this->modelsNamespace,
            ]);
        }

        $this->stub = str_replace(
            '{{BelongsToRelations}}',
            $relationsCompiled,
            $this->stub
        );
    }

    /**
     * @param string $tableName
     */
    private function compileHasManyRelations(string $tableName)
    {
        //get all foreign keys
        $foreignKeys = $this->schema->listForeignKeys();

        //get all foreign keys, where foreign table is equal to this table
        $filteredForeignKeys = [];
        foreach ($foreignKeys as $foreignKey) {
            if ($foreignKey->getForeignTableName() === $tableName ){
                array_push($filteredForeignKeys, $foreignKey);
            }
        }

        //get "has many" relations and call compiler for each
        $relationsCompiled = '';
        foreach ($filteredForeignKeys as $foreignKey){

            $localTableName = $foreignKey->getLocalTableName();

            $modelName = Helper::tableNameToModelName($localTableName, $this->dbTablePrefix);

            $relationCompiler = new HasManyRelationCompiler();
            $relationsCompiled .= $relationCompiler->compile([
                'modelName' => $modelName,
                'foreignKey' => $foreignKey->getColumns()[0],
                'modelsNamespace' => $this->modelsNamespace,
            ]);
        }

        //{{HasManyRelations}}
        $this->stub = str_replace(
            '{{HasManyRelations}}',
            $relationsCompiled,
            $this->stub
        );
    }

    /**
     * @param string $tableName
     * TODO CHECK IT - this function is not tested yet
     * TODO Change this belongsToMany relation detection algorithm
     */
    private function compileBelongsToManyRelations(string $tableName)
    {
        //
        $belongsToManyForeignKeys = $this->schema->listBelongsToManyForeignKeys($tableName);

        //get relations and call compiler for each
        $relationsCompiled = '';
        foreach ($belongsToManyForeignKeys as $belongsToManyForeignKey){
            $relationCompiler = new BelongsToManyRelationCompiler();

            $relatedModelStudlyCasePlural = '';//todo
            $relatedModelCamelCasePlural = '';//todo
            $relatedModelCamelCaseSingular = '';//todo
            $pivotTableName = '';//todo

            $relationsCompiled .= $relationCompiler->compile([
                'relatedModelStudlyCasePlural' => $relatedModelStudlyCasePlural,
                'relatedModelCamelCasePlural' => $relatedModelCamelCasePlural,
                'relatedModelCamelCaseSingular' => $relatedModelCamelCaseSingular,
                'pivotTableName' => $pivotTableName,
                'modelsNamespace' => $this->modelsNamespace,
            ]);
        }

        $this->stub = str_replace(
            '{{BelongsToManyRelations}}',
            $relationsCompiled,
            $this->stub
        );
    }


}