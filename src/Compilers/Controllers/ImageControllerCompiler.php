<?php

namespace TMPHP\RestApiGenerators\Compilers\Controllers;


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