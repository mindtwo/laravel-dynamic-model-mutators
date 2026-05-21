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
     * The name of the mutation handler.
     *
     * This name also serves as the default property on the model that holds
     * the per-attribute configuration for the registered mutators.
     *
     * @var string
     */
    protected $name;

    /**
     * The model instance the handler is currently bound to.
     *
     * @var Model
     */
    protected $model;

    /**
     * The registered mutator settings keyed by operator.
     *
     * Each entry stores the callable, the stacking flag, and the name of the
     * model property that lists the attributes governed by the mutator.
     *
     * @var array<string, array{callable: string|callable, stack: bool, property: string}>
     */
    protected $mutators = [];

    /**
     * Create a new mutation handler instance.
     *
     * @return void
     */
    public function __construct(string $name, array $getMutator = [], array $setMutator = [])
    {
        $this->name = $name;

        // We allow callers to pass the get and set mutator arguments inline so a
        // handler can be fully configured in a single expression. When given,
        // we forward them to the matching register* method via argument unpacking.
        if (count($getMutator) > 0) {
            $this->registerGetMutator(...$getMutator);
        }

        if (count($setMutator) > 0) {
            $this->registerSetMutator(...$setMutator);
        }
    }

    /**
     * Set the model the handler should operate on.
     *
     * @return $this
     */
    public function setModel(Model &$model): self
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get the name of the mutation handler.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the configuration entry for the given operator.
     *
     * When a specific key is provided, only that value is returned. Otherwise
     * the full operator configuration array is returned.
     *
     * @return mixed
     */
    protected function config(string $operator, ?string $key = null)
    {
        if (isset($this->mutators[$operator])) {
            return is_null($key) ? $this->mutators[$operator] : $this->mutators[$operator][$key];
        }
    }

    /**
     * Register a mutator for the given operator.
     *
     * @return $this
     */
    protected function registerMutator(string $operator, string|callable $callable, bool $stack = false, ?string $property = null): self
    {
        $this->mutators[$operator] = [
            'callable' => $callable,
            'stack' => $stack,
            'property' => $property ?? $this->name,
        ];

        return $this;
    }

    /**
     * Register the callable that should be invoked when setting an attribute.
     *
     * @return $this
     */
    public function registerSetMutator(string|callable $callable, bool $stack = false, ?string $property = null): self
    {
        return $this->registerMutator(self::OPERATOR_SET, $callable, $stack, $property);
    }

    /**
     * Register the callable that should be invoked when getting an attribute.
     *
     * @return $this
     */
    public function registerGetMutator(string|callable $callable, bool $stack = false, ?string $property = null): self
    {
        return $this->registerMutator(self::OPERATOR_GET, $callable, $stack, $property);
    }

    /**
     * Return the given attribute name if a mutator is defined for it, otherwise throw.
     *
     *
     * @throws DynamicMutatorNotDefinedException
     */
    protected function mutatorNameOrFail(string $name, string $operator): string
    {
        if (! $this->hasMutator($name, $operator)) {
            throw new DynamicMutatorNotDefinedException('Mutator not defined!', 0, null, [
                'name' => $name,
                'operator' => $operator,
                'handler' => $this->getName(),
            ]);
        }

        return $name;
    }

    /**
     * Resolve the configured callable for the given operator or throw.
     *
     * Method-name strings are bound to the current model instance so callers
     * can reference protected methods declared on the model itself.
     *
     *
     * @throws DynamicMutatorNotCallableException
     */
    protected function callableOrFail(string $operator): callable
    {
        $callable = $this->config($operator, 'callable');

        // If the configured value is a string and refers to a method on the
        // bound model, we'll build a [$model, $method] tuple so the mutator
        // executes in the context of the model instance.
        if (is_string($callable) && method_exists($this->model, $callable)) {
            /** @var callable $methodCallable */
            $methodCallable = [$this->model, $callable];

            return $methodCallable;
        }

        if (is_callable($callable)) {
            return $callable;
        }

        throw new DynamicMutatorNotCallableException('The given callable is invalid!', 0, null, [
            'callable' => $callable,
            'handler' => $this->getName(),
        ]);
    }

    /**
     * Determine if a mutator is registered for the given attribute and operator.
     */
    protected function hasMutator(string $name, string $operator): bool
    {
        return isset($this->mutators[$operator])
            && $this->getMutatorProperties($operator)->has($name);
    }

    /**
     * Determine if a "get" mutator is registered for the given attribute.
     */
    public function hasGetMutator(string $name): bool
    {
        return $this->hasMutator($name, self::OPERATOR_GET);
    }

    /**
     * Determine if a "set" mutator is registered for the given attribute.
     */
    public function hasSetMutator(string $name): bool
    {
        return $this->hasMutator($name, self::OPERATOR_SET);
    }

    /**
     * Get the per-attribute configuration for the given operator.
     *
     * The configuration is read from the model property whose name matches
     * the operator's "property" setting and is wrapped in a collection for
     * convenient lookup.
     *
     * @return Collection<array-key, mixed>
     */
    protected function getMutatorProperties(string $operator): Collection
    {
        $property = $this->config($operator, 'property');

        /** @var array<array-key, mixed> $values */
        $values = $property ? ($this->model->{$property} ?? []) : [];

        return collect($values);
    }

    /**
     * Invoke the registered "get" mutator for the given attribute.
     *
     *
     * @throws DynamicMutatorNotCallableException
     * @throws DynamicMutatorNotDefinedException
     */
    public function callGetMutator(string $name): mixed
    {
        $name = $this->mutatorNameOrFail($name, self::OPERATOR_GET);
        $callable = $this->callableOrFail(self::OPERATOR_GET);

        // The mutator receives the attribute name along with its per-attribute
        // configuration so the same callable can drive any number of attributes
        // while still tailoring its behavior to each one.
        return call_user_func_array($callable, [
            $name,
            $this->getMutatorProperties(self::OPERATOR_GET)->get($name),
        ]);
    }

    /**
     * Invoke the registered "set" mutator for the given attribute.
     *
     *
     * @throws DynamicMutatorNotCallableException
     * @throws DynamicMutatorNotDefinedException
     */
    public function callSetMutator(string $name, mixed $value): mixed
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
     * Determine whether the parent setter should run after this handler.
     *
     * Stacking lets multiple handlers participate in the same attribute write
     * instead of the first match short-circuiting the chain.
     */
    public function shouldStack(string $operator): bool
    {
        return (bool) ($this->config($operator, 'stack') ?? false);
    }
}
