<?php

namespace Mindtwo\DynamicMutators\Interfaces;

interface MutationHandlerInterface
{
    const OPERATOR_GET = 'get';
    const OPERATOR_SET = 'set';

    public function getName(): string;

    public function registerSetMutator($callable, bool $stack = false, string $property = null);

    public function registerGetMutator($callable, bool $stack = false, string $property = null);

    public function hasGetMutator(string $name): bool;

    public function hasSetMutator(string $name): bool;

    public function callGetMutator(string $name);

    public function callSetMutator(string $name, $value);

    public function setModel(&$model);
}
