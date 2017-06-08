<?php

namespace TMPHP\RestApiGenerators\Compilers\Scopes;

use TMPHP\RestApiGenerators\AbstractEntities\StubCompilerAbstract;

/**
 * Class RelatedWhereIntegerScopeCompiler
 * @package TMPHP\RestApiGenerators\Compilers\Scopes
 */
class RelatedWhereIntegerScopeCompiler extends StubCompilerAbstract
{

    /**
     * RelatedWhereIntegerScopeCompiler constructor.
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
     *
     * @return string
     */
    public function compile(array $params): string
    {
        /** @var \Doctrine\DBAL\Schema\Column $column */
        $column = $params['column'];
        $columnName = $column->getName();

        //
        $this->replaceInStub([
            '{{model}}' => $params['model'],
            '{{relation}}' => $params['relation'],
            '{{columnName}}' => $columnName,
            '{{columnNameStudlyCase}}' => studly_case($columnName),
        ]);

        return $this->stub;
    }
}