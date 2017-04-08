<?php

namespace TMPHP\RestApiGenerators\Compilers;


use Illuminate\Support\Facades\Log;
use TMPHP\RestApiGenerators\Core\StubCompiler;

class RulesArrayCompiler extends StubCompiler
{

    /**
     * RulesArrayCompiler constructor.
     * @param null $saveToPath
     * @param null $saveFileName
     * @param null $stub
     */
    public function __construct($saveToPath = null, $saveFileName = null, $stub = null)
    {
        $saveToPath = storage_path('CRUD/Models/');
        $saveFileName = '';

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

        //get list of fields for fillable array
        $fields = '';
        foreach ($columns as $column) {
            if (!$column->getAutoincrement()) {
                switch ($column->getType()) {
                    case 'Boolean':
                        $fields .= "'{$column->getName()}' => 'boolean', \n";
                        break;

                    case 'Integer':
                        $fields .= "'{$column->getName()}' => 'integer', \n";
                        break;

                    case 'SmallInt'://todo specify ranges
                        $fields .= "'{$column->getName()}' => 'integer', \n";
                        break;

                    case 'Float':
                        $fields .= "'{$column->getName()}' => 'numeric', \n";
                        break;

                    case 'Decimal':
                        $fields .= "'{$column->getName()}' => 'numeric', \n";
                        break;

                    case 'BigInt':
                        $fields .= "'{$column->getName()}' => 'numeric', \n";
                        break;

                    case 'String':
                        $fields .= "'{$column->getName()}' => 'string', \n";
                        break;
                    default:
                        break;
                }
            }
        }

        //
        $this->stub = str_replace(
            '{{fields}}',
            $fields,
            $this->stub
        );

        //
        return $this->stub;
    }

}