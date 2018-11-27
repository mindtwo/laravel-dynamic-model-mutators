<?php

namespace mindtwo\LaravelDynamicModelMutators;

use mindtwo\LaravelDynamicModelMutators\Exceptions\MutatorNotCallableException;

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
     * @param mixed $key
     * @param mixed $value
     *
     * @return Model
     */
    public function setAttribute($key, $value)
    {
        foreach (self::$dynamic_mutators['set'] ?? [] as $mutatorKey => $callable) {
            if ($this->checkDynamicMutatorValues($mutatorKey, $key)) {
                return $this->$callable($key, $value);
            }
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param mixed $key
     *
     * @return mixed
     */
    public function getAttribute($key)
    {
        foreach (self::$dynamic_mutators['get'] ?? [] as $mutatorKey => $callable) {
            if ($this->checkDynamicMutatorValues($mutatorKey, $key)) {
                return $this->$callable($key);
            }
        }

        return parent::getAttribute($key);
    }

    /**
     * @param $mutatorKey
     * @param $key
     *
     * @return bool
     */
    protected function checkDynamicMutatorValues($mutatorKey, $key): bool
    {
        if (! is_array($this->$mutatorKey)) {
            return false;
        }

        return ! empty($this->$mutatorKey[$key]) || in_array($key, $this->$mutatorKey);
    }

    /**
     * Register defined set mutators.
     *
     * @param string $name
     * @param string $callableMethod
     *
     * @throws MutatorNotCallableException
     */
    protected static function registerSetMutator(string $name, string $callableMethod)
    {
        self::registerMutator('set', $name, $callableMethod);
    }

    /**
     * Register defined get mutators.
     *
     * @param string $name
     * @param string $callableMethod
     *
     * @throws MutatorNotCallableException
     */
    protected static function registerGetMutator(string $name, string $callableMethod)
    {
        self::registerMutator('get', $name, $callableMethod);
    }

    /**
     * Register defined get mutators.
     *
     * @param string $operator
     * @param string $name
     * @param string $callableMethod
     *
     * @throws MutatorNotCallableException
     */
    protected static function registerMutator(string $operator, string $name, string $callableMethod)
    {
        if (! is_callable([new static(), $callableMethod]) || ! method_exists(new static(), $callableMethod)) {
            throw new MutatorNotCallableException(sprintf('"%s" Mutator for "%s" is not callable', $operator, $name));
        }

        self::$dynamic_mutators[$operator][$name] = $callableMethod;
    }
}
