<?php

namespace TMPHP\RestApiGenerators\Compilers;


use TMPHP\RestApiGenerators\AbstractEntities\StubCompilerAbstract;

/**
 * Class CrudModelRoutesCompiler
 * @package TMPHP\RestApiGenerators\Compilers
 */
class CrudModelRoutesCompiler extends StubCompilerAbstract
{

    /**
     * @var string
     */
    private $controllersNamespace;

    /**
     * CrudModelRoutesCompiler constructor.
     *
     * @param null $saveToPath
     * @param null $saveFileName
     * @param null $stub
     */
    public function __construct($saveToPath = null, $saveFileName = null, $stub = null)
    {
        $saveToPath = base_path(config('rest-api-generator.paths.routes'));
        $saveFileName = '';

        $this->controllersNamespace = config('rest-api-generator.namespaces.controllers');

        parent::__construct($saveToPath, $saveFileName, $stub);
    }

    /**
     * @param array $params
     * @return bool|mixed|string
     */
    public function compile(array $params): string
    {

        //
        $modelSingularLowercase = $params['modelName'];

        //
        $modelPlurarLowercase = $this->pluralizeKebabCase($params['modelName']);

        //
        $modelSingularUppercase = '';
        foreach (explode('-', $params['modelName']) as $pivotModelNamePart) {
            $modelSingularUppercase .= ucfirst($pivotModelNamePart);
        }

        //
        $this->replaceInStub([
            '{{ModelSingularLowercase}}' => $modelSingularLowercase,
            '{{ModelPlurarLowercase}}' => $modelPlurarLowercase,
            '{{ModelSingularUppercase}}' => $modelSingularUppercase,
            '{{controllersNamespace}}' => $this->controllersNamespace,
        ]);

        //
        return $this->stub;
    }

    /**
     * Pluralize string from kebab case. //todo move to helper and replace all usages
     *
     * @param string $string example: user-role
     * @return string
     */
    private function pluralizeKebabCase(string $string): string
    {
        //
        $subStrings = explode('-', $string);

        //
        $pluralizedSubStrings = [];
        foreach ($subStrings as $subString) {
            array_push($pluralizedSubStrings, str_plural($subString));
        }

        //
        $result = implode('-', $pluralizedSubStrings);

        return $result;
    }

}