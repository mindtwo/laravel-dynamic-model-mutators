<?php

namespace Mindtwo\DynamicMutators\Handler;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Mindtwo\DynamicMutators\Exceptions\DynamicMutatorNotCallableException;
use Mindtwo\DynamicMutators\Exceptions\DynamicMutatorNotDefinedException;
use Mindtwo\DynamicMutators\Interfaces\MutationHandlerInterface;

class MutationHandler implements MutationHandlerInterface
{
    /**
     * Mutator name.
     *
     * @var string
     */
    protected $name;

    /**
     * Related model.
     *
     * @var Model
     */
    protected $model;

    /**
     * Mutator settings.
     *
     * @var array
     */
    protected $mutators = [];

    /**
     * Mutation handler constructor.
     *
     * @param string $name
     */
    public function __construct(string $name, array $getMutator = [], array $setMutator = [])
    {
        $this->name = $name;

        if (count($getMutator) > 0) {
            $this->registerGetMutator(...$getMutator);
        }

        if (count($setMutator) > 0) {
            $this->registerSetMutator(...$setMutator);
        }
    }

    /**
     * Set related model.
     *
     * @param $model
     *
     * @return MutationHandler
     */
    public function setModel(&$model): self
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get mutation handler name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get mutator config.
     *
     * @param string $operator
     * @param string $key
     *
     * @return mixed
     */
    protected function config(string $operator, string $key = null)
    {
        if (isset($this->mutators[$operator])) {
            return is_null($key) ? $this->mutators[$operator] : $this->mutators[$operator][$key];
        }
    }

    /**
     * Register mutator.
     *
     * @param string          $operator
     * @param callable|string $callable
     * @param bool            $stack
     * @param string|null     $property
     *
     * @return MutationHandler
     */
    protected function registerMutator(string $operator, $callable, bool $stack = false, string $property = null): self
    {
        $this->mutators[$operator] = [
            'callable' => $callable,
            'stack'    => $stack,
            'property' => $property ?? $this->name,
        ];

        return $this;
    }

    /**
     * Register set mutator.
     *
     * @param callable|string $callable
     * @param bool            $stack
     * @param string|null     $property
     *
     * @return MutationHandler
     */
    public function registerSetMutator($callable, bool $stack = false, string $property = null): self
    {
        return $this->registerMutator(self::OPERATOR_SET, $callable, $stack, $property);
    }

    /**
     * Register get mutator.
     *
     * @param callable|string $callable
     * @param bool            $stack
     * @param string|null     $property
     *
     * @return MutationHandler
     */
    public function registerGetMutator($callable, bool $stack = false, string $property = null): self
    {
        return $this->registerMutator(self::OPERATOR_GET, $callable, $stack, $property);
    }

    /**
     * Return a valid mutator name or throw an exceptions.
     *
     * @param string $name
     * @param string $operator
     *
     * @throws DynamicMutatorNotDefinedException
     *
     * @return string
     */
    protected function mutatorNameOrFail(string $name, string $operator): string
    {
        if (! $this->hasMutator($name, $operator)) {
            throw new DynamicMutatorNotDefinedException('Mutator not defined!', 0, null, [
                'name'     => $name,
                'operator' => $operator,
                'handler'  => $this->getName(),
            ]);
        }

        return $name;
    }

    /**
     * Get callable or throw an exception.
     *
     * @param $callable
     *
     * @throws DynamicMutatorNotCallableException
     *
     * @return callable
     */
    protected function callableOrFail(string $operator): callable
    {
        $callable = $this->config($operator, 'callable');

        if (is_string($callable) && method_exists($this->model, $callable)) {
            return [$this->model, $callable];
        }

        if (is_callable($callable)) {
            return $callable;
        }

        throw new DynamicMutatorNotCallableException('The given callable is invalid!', 0, null, [
            'callable' => $callable,
            'handler'  => $this->getName(),
        ]);
    }

    /**
     * Determinate if a mutator exists.
     *
     * @param string $name
     * @param string $operator
     *
     * @return bool
     */
    protected function hasMutator(string $name, string $operator): bool
    {
        return isset($this->mutators[$operator])
            && $this->getMutatorProperties($operator)->has($name);
    }

    /**
     * Determinate if a get mutator exists.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasGetMutator(string $name): bool
    {
        return $this->hasMutator($name, self::OPERATOR_GET);
    }

    /**
     * Determinate if a set mutator exists.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasSetMutator(string $name): bool
    {
        return $this->hasMutator($name, self::OPERATOR_SET);
    }

    protected function getMutatorProperties(string $operator): Collection
    {
        return collect($this->model->{$this->config($operator, 'property')} ?? []);
    }

    public function callGetMutator(string $name)
    {
        $name = $this->mutatorNameOrFail($name, self::OPERATOR_GET);
        $callable = $this->callableOrFail(self::OPERATOR_GET);

        return call_user_func_array($callable, [
            $name,
            $this->getMutatorProperties(self::OPERATOR_GET)->get($name),
        ]);
    }

    public function callSetMutator(string $name, $value)
    {
        $name = $this->mutatorNameOrFail($name, self::OPERATOR_SET);
        $callable = $this->callableOrFail(self::OPERATOR_SET);

        return call_user_func_array($callable, [
            $name,
            $value,
            $this->getMutatorProperties(self::OPERATOR_SET)->get($name),
        ]);
    }

    /**
     * Determinate if the next mutator should be called.
     *
     * @param $operator
     *
     * @return bool
     */
    public function shouldStack($operator): bool
    {
        return $this->config($operator, 'stack');
    }
}
