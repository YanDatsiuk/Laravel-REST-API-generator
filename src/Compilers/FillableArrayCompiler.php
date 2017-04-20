<?php

namespace TMPHP\RestApiGenerators\Compilers;


use Illuminate\Support\Facades\Log;
use TMPHP\RestApiGenerators\Core\StubCompilerAbstract;

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
        $saveToPath = storage_path('CRUD/Models/');
        $saveFileName = '';

        parent::__construct($saveToPath, $saveFileName, $stub);
    }

    /**
     * @param array $params
     * @return bool|mixed|string
     */
    public function compile(array $params):string
    {
        /**
         * @var \Doctrine\DBAL\Schema\Column[]
         */
        $columns = $params['columns'];

        //get list of fields for fillable array
        $fields = '';
        foreach ($columns as $column){
            if (!$column->getAutoincrement()){
                $fields .= "'{$column->getName()}', \n";
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