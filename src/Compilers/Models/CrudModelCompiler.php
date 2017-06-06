<?php

namespace TMPHP\RestApiGenerators\Compilers\Models;


use TMPHP\RestApiGenerators\AbstractEntities\StubCompilerAbstract;
use TMPHP\RestApiGenerators\Compilers\Core\FillableArrayCompiler;
use TMPHP\RestApiGenerators\Compilers\Scopes\WhereFloatScopeCompiler;
use TMPHP\RestApiGenerators\Compilers\Scopes\WhereIntegerScopeCompiler;
use TMPHP\RestApiGenerators\Compilers\Scopes\WhereStringScopeCompiler;
use TMPHP\RestApiGenerators\Support\Helper;
use TMPHP\RestApiGenerators\Support\SchemaManager;

/**
 * Class CrudModelCompiler
 * @package TMPHP\RestApiGenerators\Compilers
 */
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
     * @var string
     */
    private $tableName;

    /**
     * @var string
     */
    private $modelName;

    /**
     * CrudModelCompiler constructor.
     *
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
        $this->tableName = $params['tableName'];
        $this->modelName = ucfirst($params['modelName']);

        //{{FillableArray}}
        $this->compileFillableArray();

        //{{BelongsToRelations}}
        $this->compileBelongsToRelations();

        //{{HasManyRelations}}
        $this->compileHasManyRelations();

        //{{BelongsToManyRelations}}
        $this->compileBelongsToManyRelations();

        //{{DynamicScopes}}
        $this->compileDynamicScopes();


        //
        $this->replaceInStub(
            [
                '{{ModelCapitalized}}' => $this->modelName,
                '{{table_name}}' => $this->tableName,
                '{{modelsNamespace}}' => $this->modelsNamespace
            ]
        );

        //
        $this->saveStub();

        //
        return $this->stub;
    }

    /**
     *
     */
    private function compileFillableArray()
    {
        /**
         * @var \Doctrine\DBAL\Schema\Column[]
         */
        $columns = $this->schema->listTableColumns($this->tableName);

        $fillableArrayCompiler = new FillableArrayCompiler();
        $fillableArrayCompiled = $fillableArrayCompiler->compile(['columns' => $columns]);

        //{{FillableArray}}
        $this->replaceInStub(['{{FillableArray}}' => $fillableArrayCompiled]);
    }

    /**
     *
     */
    private function compileBelongsToRelations()
    {
        /** @var  $foreignKeys \Doctrine\DBAL\Schema\ForeignKeyConstraint[] */
        $foreignKeys = $this->schema->listTableForeignKeys($this->tableName);

        $relationsCompiled = '';

        //get relations and call compiler for each
        foreach ($foreignKeys as $foreignKey) {

            $foreignTableName = $foreignKey->getForeignTableName();

            $relatedModelName = Helper::tableNameToModelName($foreignTableName, $this->dbTablePrefix);
            $belongToRelationName = Helper::columnNameToBelongToRelationName($foreignKey->getColumns()[0]);//todo

            $relationCompiler = new BelongsToRelationCompiler();
            $relationsCompiled .= "\n\n\t" . $relationCompiler->compile([
                    'relatedModelName' => $relatedModelName,
                    'belongToRelationName' => $belongToRelationName,
                    'modelsNamespace' => $this->modelsNamespace,
                ]);
        }

        //{{BelongsToRelations}}
        $this->replaceInStub(['{{BelongsToRelations}}' => $relationsCompiled]);
    }

    /**
     *
     */
    private function compileHasManyRelations()
    {
        //get all foreign keys
        $foreignKeys = $this->schema->listForeignKeys();

        //get all foreign keys, where foreign table is equal to this table
        $filteredForeignKeys = [];
        foreach ($foreignKeys as $foreignKey) {
            if ($foreignKey->getForeignTableName() === $this->tableName) {
                array_push($filteredForeignKeys, $foreignKey);
            }
        }

        //get "has many" relations and call compiler for each
        $relationsCompiled = '';
        foreach ($filteredForeignKeys as $foreignKey) {

            $localTableName = $foreignKey->getLocalTableName();

            $modelName = Helper::tableNameToModelName($localTableName, $this->dbTablePrefix);

            $relationCompiler = new HasManyRelationCompiler();
            $relationsCompiled .= "\n\n\t" . $relationCompiler->compile([
                    'modelName' => $modelName,
                    'foreignKey' => $foreignKey->getColumns()[0],
                    'modelsNamespace' => $this->modelsNamespace,
                ]);
        }

        //{{HasManyRelations}}
        $this->replaceInStub(['{{HasManyRelations}}' => $relationsCompiled]);
    }

    /**
     *
     */
    private function compileBelongsToManyRelations()
    {
        //get all foreign keys, which have "belongs to many" nature
        $belongsToManyForeignKeys = $this->schema->listBelongsToManyForeignKeys($this->tableName);

        //compile all "belongs to many" relations
        $relationsCompiled = '';
        foreach ($belongsToManyForeignKeys as $belongsToManyForeignKey) {

            //init related model in the "belongs to many" relation
            $relatedModel = Helper::tableNameToModelName($belongsToManyForeignKey->getForeignTableName());
            $relatedModelStudlyCasePlural = studly_case(str_plural($relatedModel));
            $relatedModelStudlyCaseSingular = studly_case($relatedModel);
            $relatedModelCamelCaseSingular = camel_case($relatedModel);
            $pivotTableName = $belongsToManyForeignKey->getLocalTableName();
            $foreignKey = $this->schema->getKeyInTableWhichPointsToTable($pivotTableName, $this->tableName);
            $relationName = $this->guessBelongsToManyRelationName($relatedModel, $pivotTableName, $relationsCompiled);

            $relationCompiler = new BelongsToManyRelationCompiler();
            $relationsCompiled .= "\n\n\t" . $relationCompiler->compile([
                    'relatedModelStudlyCasePlural' => $relatedModelStudlyCasePlural,
                    'relatedModelStudlyCaseSingular' => $relatedModelStudlyCaseSingular,
                    'relationName' => $relationName,
                    'relatedModelCamelCaseSingular' => $relatedModelCamelCaseSingular,
                    'pivotTableName' => $pivotTableName,
                    'modelsNamespace' => $this->modelsNamespace,
                    'foreignKey' => $foreignKey->getLocalColumns()[0],
                    'relatedKey' => $belongsToManyForeignKey->getLocalColumns()[0]
                ]);
        }

        //{{BelongsToManyRelations}}
        $this->replaceInStub(['{{BelongsToManyRelations}}' => $relationsCompiled]);
    }

    /**
     * Get name for a "belongs to many" relation.
     * Check whether relation name is already generated in stub, or in $relationsCompiled.
     *
     * @param string $relatedModel
     * @param string $pivotTableName
     * @param string $relationsCompiled
     * @return string
     */
    private function guessBelongsToManyRelationName(
        string $relatedModel,
        string $pivotTableName,
        string $relationsCompiled
    ) {
        //set relation name
        $relationName = camel_case(str_plural($relatedModel));

        //check whether relation name is already generated in stub, or in $relationsCompiled.
        if (str_contains($this->stub . $relationsCompiled, $relationName . '()')) {
            $relationName = $pivotTableName . '_' . $relationName;
        }

        return $relationName;
    }

    /**
     * Compile dynamic scopes for model
     */
    private function compileDynamicScopes()
    {
        //todo

        $scopedCompiled = "\n";

        /**
         * @var \Doctrine\DBAL\Schema\Column[] local table columns
         */
        $columns = $this->schema->listTableColumns($this->tableName);

        //compile scope for each local column
        foreach ($columns as $column) {
            $type = $column->getType();
            if ($type == 'Integer' || $type == 'SmallInt' || $type == 'BigInt') {
                $whereScope = new WhereIntegerScopeCompiler();
                $scopedCompiled .= $whereScope->compile(['column' => $column]);
            }

            if ($type == 'Float' || $type == 'Decimal') {
                $whereScope = new WhereFloatScopeCompiler();
                $scopedCompiled .= $whereScope->compile(['column' => $column]);
            }

            if ($type == 'String') {
                $whereScope = new WhereStringScopeCompiler();
                $scopedCompiled .= $whereScope->compile(['column' => $column]);
            }
        }

        //get all model relations

        //for each model relation compile scope for each related table column

        //{{DynamicScopes}}
        $this->replaceInStub(['{{DynamicScopes}}' => $scopedCompiled]);
    }

}