<?php

namespace TMPHP\RestApiGenerators\Compilers;


use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Schema\Table;
use Illuminate\Support\Facades\Log;
use TMPHP\RestApiGenerators\AbstractEntities\StubCompilerAbstract;
use TMPHP\RestApiGenerators\Support\SchemaManager;

/**
 * Class RulesArrayCompiler
 * @package TMPHP\RestApiGenerators\Compilers
 */
class RulesArrayCompiler extends StubCompilerAbstract
{

    /**
     * @var string
     */
    private $tableName;

    /**
     * @var SchemaManager
     */
    private $schema;

    /**
     * @var Table
     */
    private $table;

    /**
     * @var ForeignKeyConstraint[]
     */
    private $foreignKeys;

    /**
     * RulesArrayCompiler constructor.
     * @param null $saveToPath
     * @param null $saveFileName
     * @param null $stub
     */
    public function __construct($saveToPath = null, $saveFileName = null, $stub = null)
    {
        $saveToPath = base_path(config('rest-api-generator.paths.models'));
        $saveFileName = '';

        $this->schema = new SchemaManager();

        parent::__construct($saveToPath, $saveFileName, $stub);
    }

    /**
     * @param array $params
     * @return bool|mixed|string
     */
    public function compile(array $params): string
    {
        /**
         * @var \Doctrine\DBAL\Schema\Column[]
         */
        $columns = $params['columns'];
        $this->tableName = $params['tableName'];
        $this->table = $this->schema->listTableDetails($this->tableName);
        $this->foreignKeys = $this->table->getForeignKeys();

        //get list of fields for fillable array
        $fields = '';
        foreach ($columns as $column) {
            if (!$column->getAutoincrement()) {
                switch ($column->getType()) {
                    case 'Boolean':
                        $fields .= "'{$column->getName()}' => '{$this->getRulesForColumn($column, 'boolean')}', \n\t\t\t";
                        break;

                    case 'Integer':
                        $fields .= "'{$column->getName()}' => '{$this->getRulesForColumn($column, 'integer')}', \n\t\t\t";
                        break;

                    case 'SmallInt':
                        $fields .= "'{$column->getName()}' => '{$this->getRulesForColumn($column, 'integer')}', \n\t\t\t";
                        break;

                    case 'Float':
                        $fields .= "'{$column->getName()}' => '{$this->getRulesForColumn($column, 'numeric')}', \n\t\t\t";
                        break;

                    case 'Decimal':
                        $fields .= "'{$column->getName()}' => '{$this->getRulesForColumn($column, 'numeric')}', \n\t\t\t";
                        break;

                    case 'BigInt':
                        $fields .= "'{$column->getName()}' => '{$this->getRulesForColumn($column, 'numeric')}', \n\t\t\t";
                        break;

                    case 'String':
                        $fields .= "'{$column->getName()}' => '{$this->getRulesForColumn($column, 'string')}', \n\t\t\t";
                        break;
                    default:
                        break;
                }
            }
        }

        //
        $this->replaceInStub([
            '{{storeRules}}' => $fields,
            '{{updateRules}}' => $fields,
            '{{indexRules}}' => '',//todo add some validation fields
            '{{showRules}}' => '',//todo add some validation fields
            '{{destroyRules}}' => '',//todo add some validation fields
        ]);


        //
        return $this->stub;
    }

    /**
     * Get rules for column in table
     *
     * @param Column $column
     * @param string $firstRule
     * @return string
     */
    private function getRulesForColumn(Column $column, string $firstRule = 'string'): string
    {
        $rules = $firstRule;
        $columnName = $column->getName();
        $columnType = $column->getType(); //$column->getUnsigned()

        //v- date_time //todo add validation rules for different sql date time column types

        //
        if ($columnType == 'SmallInt') {
            $rules .= $column->getUnsigned() ? '|between:0,65535' : '|between:-32768,32767';
        }

        //
        if ($columnType == 'Integer') {
            $rules .= $column->getUnsigned() ? '|between:0,4294967295' : '|between:-2147483648,2147483647';
        }

        //
        if ($columnType == 'BigInt') {
            $rules .= $column->getUnsigned() ? '|between:0,18446744073709551615' : '|between:-9223372036854775808,9223372036854775807';
        }

        //v- email
        if (str_contains($columnName, 'mail')) {
            $rules .= '|email';
        }

        //
        $foreignRule = $this->makeForeignKeyRule($columnName);
        $rules .= $foreignRule ? $foreignRule : '';

        //v- numeric

        return $rules;
    }

    /**
     * Make validation "exists" rule. It depends on $this->foreignKeys and $columnName.
     *
     * @param string $columnName
     * @return null|string laravel validation rule or null.
     */
    private function makeForeignKeyRule(string $columnName)
    {
        $foreignRule = null;
        foreach ($this->foreignKeys as $foreignKey) {
            foreach ($foreignKey->getLocalColumns() as $localColumn) {

                if ($localColumn === $columnName) {
                    $foreignRule = '|exists:' . $foreignKey->getForeignTableName() . ',' . $foreignKey->getForeignColumns()[0];
                }
            }
        }
        return $foreignRule;
    }

}