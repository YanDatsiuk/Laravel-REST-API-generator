<?php
/**
 * Created by PhpStorm.
 * User: datsyuk
 * Date: 08.06.17
 * Time: 10:42
 */

namespace TMPHP\RestApiGenerators\Compilers\Scopes\Support;


use Illuminate\Support\Facades\Log;

class CompiledScopesHelper
{
    /** @var  string $scopes */
    private $scopes;

    /**
     * CompiledScopesHelper constructor.
     * @param string $scopes
     */
    public function __construct(string $scopes)
    {
        $this->scopes = $scopes;
    }

    /**
     * Remove duplicate methods.
     *
     * @return string
     */
    public function removeDuplicates(): string
    {
        $explodedScopes = $this->explodeScopes();

        $scopeMethodNames = $this->getMethodNames($explodedScopes);

        if ($this->isDuplicates($scopeMethodNames)) {
            $this->removeDuplicatedMethods($explodedScopes, $scopeMethodNames);
        }

        return $this->scopes;
    }

    /**
     * @return string
     */
    public function getScopes(): string
    {
        return $this->scopes;
    }

    /**
     * @param string $scopes
     * @return CompiledScopesHelper
     */
    public function setScopes(string $scopes): CompiledScopesHelper
    {
        $this->scopes = $scopes;

        return $this;
    }

    /**
     * Explode string with scopes into array of strings (methods' bodies).
     *
     * @return array
     */
    private function explodeScopes()
    {
        $explodedScopes = explode('public function ', $this->scopes);

        $explodedScopes = array_filter($explodedScopes, function ($value) {
            return str_contains($value, 'scope');
        });

        return $explodedScopes;
    }

    /**
     * Get methods' names from exploded scopes.
     *
     * @param array $explodedScopes
     * @return array
     */
    private function getMethodNames(array $explodedScopes): array
    {
        $methodNames = [];

        foreach ($explodedScopes as $scope) {

            $firstParenthesisIndex = strpos($scope, '(');
            $methodName = substr($scope, 0, $firstParenthesisIndex);
            $methodNames[] = $methodName;
        }

        return $methodNames;
    }

    /**
     * Checks whether there are duplicates in method names.
     *
     * @param array $scopeMethodNames
     * @return bool
     */
    private function isDuplicates(array $scopeMethodNames): bool
    {
        return count($scopeMethodNames) === count(array_unique($scopeMethodNames)) ? false : true;
    }

    /**
     * Remove duplicated method from $scopes property.
     *
     * @param array $explodedScopes
     * @param array $scopeMethodNames
     */
    private function removeDuplicatedMethods(array $explodedScopes, array $scopeMethodNames)
    {

        $methodNamesToMakeUnique = array_filter(array_count_values($scopeMethodNames), function ($value) {
            return $value > 1 ? true : false;
        });

        foreach ($methodNamesToMakeUnique as $methodName => $repeatQuantity) {
            for ($i = 1; $i < $repeatQuantity; $i++) {
                $this->removeDuplicatedMethod($explodedScopes, $methodName);
            }
        }
    }

    /**
     * Removes duplicated $methodName from $this->scopes.
     * Removes coincidences from the end of compiled scopes.
     *
     * @param array $explodedScopes
     * @param string $methodName
     */
    private function removeDuplicatedMethod(array $explodedScopes, string $methodName)
    {
        for ($i = count($explodedScopes) - 1; $i >= 0; $i--) {
            if (starts_with($explodedScopes[$i], $methodName.'(')) {
                $this->scopes = str_replace(
                    'public function ' . $explodedScopes[$i],
                    '',
                    $this->scopes);
                return;
            }
        }
    }
}