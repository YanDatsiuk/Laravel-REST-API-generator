<?php

namespace TMPHP\RestApiGenerators\Compilers\Core;


use TMPHP\RestApiGenerators\AbstractEntities\StubCompilerAbstract;

/**
 * Class FillableArrayCompiler
 * @package TMPHP\RestApiGenerators\Compilers
 */
class FillableArrayCompiler extends StubCompilerAbstract
{

    /**
     * FillableArrayCompiler constructor.
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
                $fields .= "'{$column->getName()}', \n\t\t";
            }
        }

        //
        $this->replaceInStub(['{{fields}}' => $fields]);

        //
        return $this->stub;
    }

}