<?php

namespace mindtwo\LaravelDynamicModelMutators;

use mindtwo\LaravelDynamicModelMutators\Exceptions\MutatorNotDefinedException;
use mindtwo\LaravelDynamicModelMutators\Exceptions\MutatorNotCallableException;
use mindtwo\LaravelDynamicModelMutators\Exceptions\MutatorOperatorNotDefinedException;

trait DynamicModelMutator
{
    /**
     * The multi-dimensional array of get and set mutators.
     *
     * @var array
     */
    private static $dynamic_mutators = [];

    /**
     * Dynamically set attributes on the model.
     *
     * @param mixed $attributeName
     * @param mixed $value
     *
     * @throws MutatorNotDefinedException
     * @throws MutatorOperatorNotDefinedException
     *
     * @return self|null
     */
    public function setAttribute($attributeName, $value): ?self
    {
        foreach (self::$dynamic_mutators['set'] ?? [] as $mutatorName => $callable) {
            if ($this->hasDynamicMutator($attributeName, $mutatorName, 'set')) {
                return $this->callDynamicMutator($attributeName, $mutatorName, $value, 'set');
            }
        }

        return parent::setAttribute($attributeName, $value);
    }

    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param mixed $attributeName
     *
     * @throws MutatorNotDefinedException
     * @throws MutatorOperatorNotDefinedException
     *
     * @return mixed
     */
    public function getAttribute($attributeName)
    {
        foreach (self::$dynamic_mutators['get'] ?? [] as $mutatorName => $callable) {
            if ($this->hasDynamicMutator($attributeName, $mutatorName)) {
                return $this->callDynamicMutator($attributeName, $mutatorName);
            }
        }

        return parent::getAttribute($attributeName);
    }

    /**
     * Call a dynamic mutator.
     *
     * @param string $attributeName
     * @param string $mutatorName
     * @param null   $value
     * @param $operator
     *
     * @throws MutatorNotDefinedException
     * @throws MutatorOperatorNotDefinedException
     *
     * @return mixed
     */
    protected function callDynamicMutator(string $attributeName, string $mutatorName, $value = null, $operator = 'get')
    {
        $method = $this->getDynamicMutatorMethodOrFail($attributeName, $mutatorName, $operator);
        $config = isset($this->$mutatorName[$attributeName]) ? $this->$mutatorName[$attributeName] : null;

        switch ($operator) {
            case 'set':
                return $this->$method($attributeName, $value, $config);
            case 'get':
                return $this->$method($attributeName, $config);
            default:
                throw new MutatorOperatorNotDefinedException();
        }
    }

    /**
     * Determinate if a dynamic mutator is defined.
     *
     * @param string $attributeName
     * @param string $mutatorName
     * @param string $operator
     *
     * @return bool
     */
    protected function hasDynamicMutator(string $attributeName, string $mutatorName, string $operator = 'get'): bool
    {
        if (! isset(self::$dynamic_mutators[$operator][$mutatorName]) || empty($this->$mutatorName)) {
            return false;
        }

        if (! array_key_exists($attributeName, $this->$mutatorName) && ! in_array($attributeName, $this->$mutatorName)) {
            return false;
        }

        return true;
    }

    /**
     * Determinate if a dynamic mutator is defined.
     *
     * @param string $attributeName
     * @param string $mutatorName
     * @param string $operator
     *
     * @throws MutatorNotDefinedException
     *
     * @return bool
     */
    protected function getDynamicMutatorMethodOrFail(string $attributeName, string $mutatorName, string $operator = 'get')
    {
        if (! $this->hasDynamicMutator($attributeName, $mutatorName, $operator)) {
            throw new MutatorNotDefinedException(sprintf('There is no "%s" mutator with name "%s" defined ', $operator, $name));
        }

        return self::$dynamic_mutators[$operator][$mutatorName];
    }

    /**
     * Register set mutator.
     *
     * @param string $mutatorName
     * @param string $methodName
     *
     * @throws MutatorNotCallableException
     * @throws MutatorOperatorNotDefinedException
     */
    protected static function registerSetMutator(string $mutatorName, string $methodName)
    {
        self::registerMutator('set', $mutatorName, $methodName);
    }

    /**
     * Register get mutator.
     *
     * @param string $mutatorName
     * @param string $methodName
     *
     * @throws MutatorNotCallableException
     * @throws MutatorOperatorNotDefinedException
     */
    protected static function registerGetMutator(string $mutatorName, string $methodName)
    {
        self::registerMutator('get', $mutatorName, $methodName);
    }

    /**
     * Register mutator.
     *
     * @param string $operator
     * @param string $mutatorName
     * @param string $methodName
     *
     * @throws MutatorNotCallableException
     * @throws MutatorOperatorNotDefinedException
     */
    protected static function registerMutator(string $operator, string $mutatorName, string $methodName)
    {
        if ('get' != $operator && 'set' != $operator) {
            throw new MutatorOperatorNotDefinedException();
        }

        if (! method_exists(new static(), $methodName)) {
            throw new MutatorNotCallableException(sprintf('"%s" Mutator for "%s" is not callable', $operator, $mutatorName));
        }

        self::$dynamic_mutators[$operator][$mutatorName] = $methodName;
    }
}
