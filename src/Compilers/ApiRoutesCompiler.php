<?php

namespace TMPHP\RestApiGenerators\Compilers;

use TMPHP\RestApiGenerators\Core\StubCompiler;

class ApiRoutesCompiler extends StubCompiler
{
    /**
     * @var
     */
    private $compiledCrudRoutesStubs;

    /**
     * ApiRoutesCompiler constructor.
     * @param string $saveToPath
     * @param null $stub
     */
    public function __construct($saveToPath = null, $saveFileName = null, $stub = null)
    {
        $saveToPath = storage_path('CRUD/Routes/');
        $saveFileName = 'api.php';

        parent::__construct($saveToPath, $saveFileName, $stub);
    }

    /**
     * @param array $params
     * @return string
     */
    public function compile(array $params):string
    {
        //generate CRUD routes for all models
        foreach ($params['models'] as $model) {
            $crudModelRoutesCompiler = new CrudModelRoutesCompiler();
            $this->compiledCrudRoutesStubs .= $crudModelRoutesCompiler->compile(['modelName' => $model]);
        }

        //insert all generated CRUD routes in the apiRoutes.stub
        $this->stub = str_replace(
            '{{crudRoutes}}',
            $this->compiledCrudRoutesStubs,
            $this->stub
        );

        //
        $this->saveStub();

        //
        return $this->stub;
    }

}