<?php

namespace TMPHP\RestApiGenerators\Helpers;

use Illuminate\Support\Facades\Validator;


/**
 * Realization Validable contract
 *
 * Class Validable
 *
 * @package App\Helpers
 */
trait Validable
{
    /**
     * Validation rules for request parameters
     *
     * @var array $rules
     */
    protected $rules = [];

    /**
     * Add validation rules to existing array of rules
     *
     * @param array $rules
     *
     * @return $this
     */
    public function addRules(array $rules)
    {
        $this->rules = array_merge($this->rules, $rules);

        return $this;
    }

    /**
     * Add or reset value of existing validation rule
     *
     * @param $keyOfRule
     * @param $valueOfRule
     *
     * @return $this
     */
    public function resetOrAddRule(string $keyOfRule, $valueOfRule)
    {
        $this->rules[$keyOfRule] = $valueOfRule;

        return $this;
    }

    /**
     * Get Validation Rules
     *
     * @return array
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * Set new array with validation rules
     *
     * @param array $rules
     *
     * @return $this
     */
    public function setRules(array $rules)
    {
        $this->rules = $rules;

        return $this;
    }

    /**
     * Delete rules by rule names parameter
     * @param array $ruleNames
     *
     * @return $this
     */
    public function deleteRules(array $ruleNames)
    {
        foreach ($ruleNames as $ruleName) {
            unset($this->rules[$ruleName]);
        }

        return $this;
    }

    /**
     * Make validator object with needed rules
     *
     * @param array $requestInputs
     * @param bool $unsetMode
     *
     * @return \Illuminate\Validation\Validator
     */
    public function checkRules(array $requestInputs, $unsetMode = true): \Illuminate\Validation\Validator
    {
        if ($unsetMode) {
            foreach ($this->rules as $key => $rule) {
                if (!array_key_exists($key, $requestInputs)) {
                    unset($this->rules[$key]);
                }
            }
        }

        return Validator::make($requestInputs, $this->rules);
    }
}