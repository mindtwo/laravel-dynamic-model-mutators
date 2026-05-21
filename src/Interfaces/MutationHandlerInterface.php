<?php

namespace Mindtwo\DynamicMutators\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface MutationHandlerInterface
{
    /**
     * The identifier used for "get" mutator operations.
     *
     * @var string
     */
    const OPERATOR_GET = 'get';

    /**
     * The identifier used for "set" mutator operations.
     *
     * @var string
     */
    const OPERATOR_SET = 'set';

    /**
     * Get the name of the mutation handler.
     */
    public function getName(): string;

    /**
     * Register the callable that should be invoked when setting an attribute.
     *
     * @return $this
     */
    public function registerSetMutator(string|callable $callable, bool $stack = false, ?string $property = null): self;

    /**
     * Register the callable that should be invoked when getting an attribute.
     *
     * @return $this
     */
    public function registerGetMutator(string|callable $callable, bool $stack = false, ?string $property = null): self;

    /**
     * Determine if a "get" mutator is registered for the given attribute.
     */
    public function hasGetMutator(string $name): bool;

    /**
     * Determine if a "set" mutator is registered for the given attribute.
     */
    public function hasSetMutator(string $name): bool;

    /**
     * Invoke the registered "get" mutator for the given attribute.
     */
    public function callGetMutator(string $name): mixed;

    /**
     * Invoke the registered "set" mutator for the given attribute.
     */
    public function callSetMutator(string $name, mixed $value): mixed;

    /**
     * Set the model the handler should operate on.
     *
     * @return $this
     */
    public function setModel(Model &$model): self;

    /**
     * Determine whether the parent setter should run after this handler.
     */
    public function shouldStack(string $operator): bool;
}
