<?php

namespace Mindtwo\DynamicMutators\Traits;

use Illuminate\Support\Str;
use Mindtwo\DynamicMutators\Interfaces\MutationHandlerInterface;

trait HasDynamicMutators
{
    /**
     * The mutation handlers registered for the model.
     *
     * @var array<int, MutationHandlerInterface>
     */
    private static $mutation_handlers = [];

    /**
     * Set the given attribute on the model.
     *
     * Each registered handler is asked whether it owns a "set" mutator for the
     * given attribute. The first handler that claims the attribute handles the
     * assignment, unless it is configured to stack with the parent setter.
     *
     * @param  string  $name
     * @param  mixed  $value
     * @return mixed
     */
    public function setAttribute($name, $value)
    {
        foreach (self::$mutation_handlers as $handler) {
            $handler->setModel($this);
            if ($handler->hasSetMutator($name)) {
                $result = $handler->callSetMutator($name, $value);

                // If the handler is not configured to stack with the parent
                // setter, we'll return immediately so the dynamic mutator is
                // the sole authority for this attribute's value.
                if (! $handler->shouldStack(MutationHandlerInterface::OPERATOR_SET)) {
                    return $result;
                }
            }
        }

        return parent::setAttribute($name, $value);
    }

    /**
     * Get the given attribute from the model.
     *
     * Each registered handler is asked whether it owns a "get" mutator for the
     * given attribute. The first handler that claims the attribute supplies
     * the returned value, otherwise the parent accessor is used.
     *
     * @param  mixed  $name
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
     * Register a mutation handler for the model.
     */
    public static function registerMutationHandler(MutationHandlerInterface $handler): void
    {
        self::$mutation_handlers[] = $handler;
    }

    /**
     * Handle dynamic method calls into the model.
     *
     * Calls matching Laravel's classic "get{Attr}Attribute" / "set{Attr}Attribute"
     * accessor and mutator naming convention are routed through the registered
     * handlers so dynamically configured attributes participate in the model's
     * appended/serialized output before falling back to Eloquent's behavior.
     *
     * @param  string  $name
     * @param  array  $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (preg_match('/^(set|get)(.+)Attribute$/', $name, $matches)) {
            $key = Str::snake($matches[2]);
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
