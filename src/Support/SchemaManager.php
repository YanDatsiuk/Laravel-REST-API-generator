<?php

namespace TMPHP\RestApiGenerators\Support;


use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class SchemaManager
 * @package TMPHP\RestApiGenerators\Support
 */
class SchemaManager
{
    /**
     * @var AbstractSchemaManager
     */
    private $schema;

    /**
     * SchemaManager constructor.
     */
    public function __construct()
    {
        $this->schema = DB::getDoctrineSchemaManager();
    }

    /**
     * Forwarding function calling to AbstractSchemaManager instance
     *
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array(array($this->schema, $name), $arguments);
    }

    /**
     * List of all foreign keys
     *
     * @return array
     */
    public function listForeignKeys()
    {
        $tables = $this->schema->listTables();
        $foreignKeys = [];
        foreach ($tables as $table) {
            foreach ($table->getForeignKeys() as $foreignKey) {
                array_push($foreignKeys, $foreignKey);
            }
        }

        return $foreignKeys;
    }


    /**
     * List of foreign keys, which have foreign table equal to $tableName
     *
     * @param string $tableName
     * @return array
     */
    public function listForeignKeysOnTable(string $tableName)
    {
        $allForeignKeys = $this->listForeignKeys();

        $foreignKeysOnTable = [];

        foreach ($allForeignKeys as $foreignKey) {
            if ($foreignKey->getForeignTableName() === $tableName) {
                array_push($foreignKeysOnTable, $foreignKey);
            }
        }

        return $foreignKeysOnTable;
    }

    /**
     * @param string $tableName
     * @return array
     */
    public function listBelongsToManyForeignKeys(string $tableName): array
    {
        $foreignKeysOnTable = $this->listForeignKeysOnTable($tableName);

        $belongsToManyForeignKeys = [];

        foreach ($foreignKeysOnTable as $foreignKeyOnTable) {

            $belongsToManyForeignKeys = array_merge(
                $belongsToManyForeignKeys,
                $this->schema->listTableForeignKeys($foreignKeyOnTable->getLocalTableName()));
        }

        //removing foreign keys, which points to $tableName
        $result = [];
        foreach ($belongsToManyForeignKeys as $belongsToManyForeignKey) {
            if ($belongsToManyForeignKey->getForeignTableName() !== $tableName) {
                array_push($result, $belongsToManyForeignKey);
            }
        }

        return $result;
    }


    /**
     *
     * @param array $tableNames Names of tables, which need to check on existance in
     * @return bool
     */
    public function existsTables(array $tableNames)
    {
        $allTableNames = $this->schema->listTableNames();

        return empty(array_diff($tableNames, $allTableNames));
    }
}