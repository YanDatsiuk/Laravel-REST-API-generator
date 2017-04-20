<?php

namespace TMPHP\RestApiGenerators\Compilers;


use Illuminate\Support\Facades\Log;
use TMPHP\RestApiGenerators\AbstractEntities\StubCompilerAbstract;

class SwaggerPropertyCompiler extends StubCompilerAbstract
{

    /**
     * SwaggerPropertyCompiler constructor.
     * @param null $saveToPath
     * @param null $saveFileName
     * @param null $stub
     */
    public function __construct($saveToPath = null, $saveFileName = null, $stub = null)
    {
        $saveToPath = base_path(config('rest-api-generator.paths.documentations'));
        $saveFileName = '';

        parent::__construct($saveToPath, $saveFileName, $stub);
    }

    /**
     * @param array $params
     * @return bool|mixed|string
     */
    public function compile(array $params):string
    {
        //
        $this->stub = str_replace(
            '{{name}}',
            strtolower($params['name']),
            $this->stub
        );

        //check type and do required transformation
        switch ($params['type']){
            case 'Time': $params['type'] = 'string'; break;
            case 'DateTime': $params['type'] = 'string'; break;
            default: break;
        }

        //
        $this->stub = str_replace(
            '{{type}}',
            strtolower($params['type']),
            $this->stub
        );

        //
        return $this->stub;
    }

}