<?php

namespace TMPHP\RestApiGenerators\Compilers;


use Illuminate\Support\Facades\Log;
use TMPHP\RestApiGenerators\AbstractEntities\StubCompilerAbstract;

/**
 * Class SwaggerPropertyCompiler
 * @package TMPHP\RestApiGenerators\Compilers
 */
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
    public function compile(array $params): string
    {
        //check type and do required transformation
        switch ($params['type']) {
            case 'Boolean':
                $params['type'] = 'boolean';
                break;
            case 'Time':
                $params['type'] = 'string';
                break;
            case 'DateTime':
                $params['type'] = 'string';
                $params['format'] = 'date-time';
                break;
            case 'Decimal':
                $params['type'] = 'number';
                $params['format'] = 'double';
                break;
            case 'Float':
                $params['type'] = 'number';
                $params['format'] = 'float';
                break;
            case 'Integer':
                $params['type'] = 'integer';
                $params['format'] = 'int32';
                break;
            case 'SmallInt':
                $params['type'] = 'integer';
                $params['format'] = 'int32';
                break;
            case 'BigInt':
                $params['type'] = 'integer';
                $params['format'] = 'int64';
                break;
            case 'Binary':
                $params['type'] = 'string';
                $params['format'] = 'binary';
                break;

            default:
                $params['type'] = 'string';
                break;
        }

        //
        $this->replaceInStub([
            '{{name}}' => strtolower($params['name']),
            '{{type}}' => strtolower($params['type']),
            '{{format}}' => strtolower($params['format']),
        ]);

        //
        return $this->stub;
    }

}