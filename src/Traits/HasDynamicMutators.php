<?php

namespace Mindtwo\DynamicMutators\Traits;

use Mindtwo\DynamicMutators\Handler\Handler;
use Mindtwo\DynamicMutators\Interfaces\MutationHandlerInterface;

trait HasDynamicMutators
{
    /**
     * Registered mutation handler.
     *
     * @var MutationHandlerInterface[]
     */
    private static $mutation_handlers = [];

    /**
     * Set attribute.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return self|null
     */
    public function setAttribute($name, $value): ?self
    {
        foreach (self::$mutation_handlers as $handler) {
            $handler->setModel($this);
            if ($handler->hasSetMutator($name)) {
                $result = $handler->callSetMutator($name, $value);

                if (! $handler->shouldStack(MutationHandlerInterface::OPERATOR_SET)) {
                    return $result;
                }
            }
        }

        return parent::setAttribute($name, $value);
    }

    /**
     * Get attribute.
     *
     * @param mixed $name
     *
     * @return mixed
     */
    public function getAttribute($name)
    {
        foreach (self::$mutation_handlers as $controller) {
            $controller->setModel($this);
            if ($controller->hasGetMutator($name)) {
                return $controller->callGetMutator($name);
            }
        }

        return parent::getAttribute($name);
    }

    /**
     * Register a mutation handler.
     *
     * @param MutationHandlerInterface $handler
     */
    public static function registerMutationHandler(MutationHandlerInterface $handler)
    {
        self::$mutation_handlers[] = $handler;
    }

    /**
     * Magic call method.
     *
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (preg_match('/^(set|get)(.+)Attribute$/', $name, $matches)) {
            $key = snake_case($matches[2]);
            foreach (self::$mutation_handlers as $handler) {
                $handler->setModel($this);
                if ($handler->hasGetMutator($key)) {
                    return $handler->callGetMutator($key);
                }
            }
        }

        return parent::__call($name, $arguments);
    }
}
