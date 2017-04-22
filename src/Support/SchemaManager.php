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
}