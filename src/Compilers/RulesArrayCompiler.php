<?php

namespace TMPHP\RestApiGenerators\Compilers;


use Illuminate\Support\Facades\Log;
use TMPHP\RestApiGenerators\AbstractEntities\StubCompilerAbstract;

/**
 * Class RulesArrayCompiler
 * @package TMPHP\RestApiGenerators\Compilers
 */
class RulesArrayCompiler extends StubCompilerAbstract
{

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

        parent::__construct($saveToPath, $saveFileName, $stub);
    }

    /**
     * @param array $params //todo send table name in $params for getting more info for generating rules
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
                        $fields .= "'{$column->getName()}' => 'boolean', \n\t\t"; //todo add 'smart' function for rule detection here
                        break;

                    case 'Integer':
                        $fields .= "'{$column->getName()}' => 'integer', \n\t\t";
                        break;

                    case 'SmallInt'://todo specify ranges
                        $fields .= "'{$column->getName()}' => 'integer', \n\t\t";
                        break;

                    case 'Float':
                        $fields .= "'{$column->getName()}' => 'numeric', \n\t\t";
                        break;

                    case 'Decimal':
                        $fields .= "'{$column->getName()}' => 'numeric', \n\t\t";
                        break;

                    case 'BigInt':
                        $fields .= "'{$column->getName()}' => 'numeric', \n\t\t";
                        break;

                    case 'String':
                        $fields .= "'{$column->getName()}' => 'string', \n\t\t";
                        break;
                    default:
                        break;
                }
            }
        }

        //
        $this->replaceInStub(['{{fields}}' => $fields]);

        //
        return $this->stub;
    }

}