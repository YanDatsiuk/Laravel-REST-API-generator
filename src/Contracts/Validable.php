<?php

namespace TMPHP\RestApiGenerators\Contracts;

use Illuminate\Validation\Validator;

/**
 * Interface Validable
 * For realization contain validation rules in Obj`s
 *
 * @package App\Contracts
 */
interface Validable
{
    public function addRules(array $rules);

    public function setRules(array $rules);

    public function getRules(): array;

    public function resetOrAddRule(string $keyOfRule, $valueOfRule);

    public function checkRules(array $requestInputs): Validator;

    public function deleteRules(array $ruleNames);
}