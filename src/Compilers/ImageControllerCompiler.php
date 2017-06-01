<?php

namespace TMPHP\RestApiGenerators\Compilers;

use Doctrine\DBAL\Schema\Column;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use TMPHP\RestApiGenerators\AbstractEntities\StubCompilerAbstract;
use TMPHP\RestApiGenerators\Support\SchemaManager;

/**
 * Class ImageControllerCompiler
 * @package TMPHP\RestApiGenerators\Compilers
 */
class ImageControllerCompiler extends CrudControllerCompiler
{
    /**
     * @param array $params
     * @return string
     */
    public function compile(array $params = []): string
    {
        $params['modelNameCamelcase'] = 'image';

        return parent::compile($params);
    }
}