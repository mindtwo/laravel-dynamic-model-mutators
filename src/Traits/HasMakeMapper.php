<?php

namespace Mindtwo\DynamicMutators\Traits;

use Illuminate\Support\Str;
use Mindtwo\DynamicMutators\Exceptions\InvalidParameterException;
use Mindtwo\DynamicMutators\Handler\MutationHandler;

trait HasMakeMapper
{
    /**
     * Get the class name of the component the facade resolves to.
     *
     * @return class-string
     */
    protected static function getFacadeAccessor()
    {
        return MutationHandler::class;
    }

    /**
     * Create a new instance of the underlying component.
     *
     * The given arguments are mapped to the constructor's parameters by name,
     * supporting both camelCase and snake_case keys. Parameters that are not
     * provided fall back to their declared default values.
     *
     *
     * @throws InvalidParameterException
     */
    public static function make(array $arguments = []): object
    {
        $class = static::getFacadeAccessor();

        // Reflect on the target class so we can introspect its constructor
        // signature and bind the caller's arguments to the right positions
        // regardless of the order in which they were provided.
        try {
            $reflection = new \ReflectionClass($class);
            $constructor = $reflection->getConstructor();

            if ($constructor === null) {
                throw new \LogicException('Class has no constructor.');
            }

            $parameters = $constructor->getParameters();
        } catch (\Throwable $error) {
            throw new \Exception('Wrong parameter', 0, $error);
        }

        return new $class(...static::composeMakeParameters($parameters, $arguments));
    }

    /**
     * Compose the positional arguments passed to the component's constructor.
     *
     * Each reflected parameter is looked up by its camelCase name first and by
     * its snake_case alias second so callers may use either naming style. When
     * neither is provided the parameter's default value is used; if no default
     * exists we throw to make the misconfiguration explicit.
     *
     * @param  array<int, \ReflectionParameter>  $parameters
     * @param  array<string, mixed>  $arguments
     * @return array<int, mixed>
     *
     * @throws InvalidParameterException
     */
    protected static function composeMakeParameters(array $parameters, array $arguments = []): array
    {
        foreach ($parameters as $key => $parameter) {
            $name = $parameter->getName();

            if (array_key_exists($name, $arguments)) {
                $value = $arguments[$name];
            } elseif (array_key_exists(Str::snake($name), $arguments)) {
                $value = $arguments[Str::snake($name)];
            } elseif ($parameter->isDefaultValueAvailable()) {
                $value = $parameter->getDefaultValue();
            } else {
                throw new InvalidParameterException('Invalid parameters for make()!', 0, null, [
                    'name' => $name,
                    'accessor' => static::getFacadeAccessor(),
                    'parameters' => $parameters,
                    'arguments' => $arguments,
                ]);
            }
            $result[$parameter->getPosition()] = $value;
        }

        return $result ?? [];
    }
}
