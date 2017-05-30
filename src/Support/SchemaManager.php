<?php

namespace TMPHP\RestApiGenerators\Support;


use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
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
     * @return ForeignKeyConstraint[]
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
     * Check whether all table from $tableNames exists in db schema
     *
     * @param array $tableNames Names of tables, which need to check on existance in
     * @return bool
     */
    public function existsTables(array $tableNames)
    {
        //get all tables names
        $allTableNames = $this->schema->listTableNames();

        return empty(array_diff($tableNames, $allTableNames));
    }

    /**
     * Get all foreign keys, which have "belongs to many" nature
     *
     * @param string $tableName
     * @return ForeignKeyConstraint[]
     */
    public function listBelongsToManyForeignKeys(string $tableName)
    {
        //get all foreign keys
        $tables = $this->schema->listTables();
        $foreignKeys = [];
        foreach ($tables as $table) {
            if (count($table->getForeignKeys()) > 1 && $this->hasTableKeysToForeignTable($table->getName(),
                    $tableName)
            ) {
                foreach ($table->getForeignKeys() as $foreignKey) {
                    $foreignKey->setLocalTable($table);
                    if ($foreignKey->getForeignTableName() !== $tableName) {
                        array_push($foreignKeys, $foreignKey);
                    }
                }
            }
        }

        return $foreignKeys;
    }

    /**
     * Check whether $localTableName has foreign keys to $foreignTableName
     *
     * @param string $localTableName
     * @param string $foreignTableName
     * @return bool
     */
    private function hasTableKeysToForeignTable(string $localTableName, string $foreignTableName)
    {
        $foreignKeys = $this->schema->listTableForeignKeys($localTableName);

        foreach ($foreignKeys as $foreignKey) {
            if ($foreignKey->getForeignTableName() === $foreignTableName) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get a foreign key in $inTable, which leads to $toTable
     *
     * @param string $inTable
     * @param string $toTable
     * @return ForeignKeyConstraint
     * @throws \Exception
     */
    public function getKeyInTableWhichPointsToTable(string $inTable, string $toTable)
    {
        $foreignKeys = $this->schema->listTableForeignKeys($inTable);

        foreach ($foreignKeys as $foreignKey){
            if ($foreignKey->getForeignTableName() === $toTable){
                return $foreignKey;
            }
        }

        throw new \Exception('No foreign key exists in table, which points to table');
    }
}