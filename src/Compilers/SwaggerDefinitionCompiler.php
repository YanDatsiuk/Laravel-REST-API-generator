<?php

namespace TMPHP\RestApiGenerators\Compilers;


use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use TMPHP\RestApiGenerators\Core\StubCompiler;

class SwaggerDefinitionCompiler extends StubCompiler
{

    /**
     * @var AbstractSchemaManager
     */
    private $schema;

    /**
     * SwaggerDefinitionCompiler constructor.
     * @param null $saveToPath
     * @param null $saveFileName
     * @param null $stub
     */
    public function __construct($saveToPath = null, $saveFileName = null, $stub = null)
    {
        $saveToPath = storage_path('CRUD/Swagger/');
        $saveFileName = '';
        $this->schema= DB::getDoctrineSchemaManager();

        parent::__construct($saveToPath, $saveFileName, $stub);
    }

    /**
     * @param array $params
     * @return bool|mixed|string
     */
    public function compile(array $params):string
    {

        //
        $this->saveFileName = $params['modelName'].'.php';

        //
        $this->stub = str_replace(
            '{{ModelLowercase}}',
            $params['modelName'],
            $this->stub
        );

        //
        $compiledProperties = '';
        $columns = $this->schema->listTableColumns($params['tableName']);

        //compile swagger properties for table columns
        foreach ($columns as $column){

            $swaggerPropertyCompiler = new SwaggerPropertyCompiler();
            $compiledProperties .= $swaggerPropertyCompiler->compile([
               'name' => $column->getName(),
                'type' => $column->getType(),
            ]);
        }

        //
        $this->stub = str_replace(
            '{{SwaggerProperties}}',
            $compiledProperties,
            $this->stub
        );

        //
        $this->saveStub();

        //
        return $this->stub;
    }

}