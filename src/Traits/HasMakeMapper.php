<?php

namespace Mindtwo\DynamicMutators\Traits;

use Illuminate\Support\Str;
use Mindtwo\DynamicMutators\Exceptions\InvalidParameterException;
use Mindtwo\DynamicMutators\Handler\MutationHandler;

trait HasMakeMapper
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return MutationHandler::class;
    }

    /**
     * Make new instance.
     *
     * @param array $arguments
     *
     * @throws InvalidParameterException
     *
     * @return object
     */
    public static function make(array $arguments = [])
    {
        $class = static::getFacadeAccessor();

        try {
            $reflection = new \ReflectionClass(static::getFacadeAccessor());
            $parameters = $reflection->getConstructor()->getParameters();
        } catch (\Throwable $error) {
            throw new \Exception('Wrong parameter', 0, $error);
        }

        return new $class(...static::composeMakeParameters($parameters, $arguments));
    }

    /**
     * Compose make() parameters.
     *
     * @param array $parameters
     * @param array $arguments
     *
     * @throws InvalidParameterException
     *
     * @return array
     */
    protected static function composeMakeParameters(array $parameters, array $arguments = []): array
    {
        foreach ($parameters as $key=>$parameter) {
            $name = $parameter->getName();

            if (array_key_exists($name, $arguments)) {
                $value = $arguments[$name];
            } elseif (array_key_exists(Str::snake($name), $arguments)) {
                $value = $arguments[Str::snake($name)];
            } elseif ($parameter->isDefaultValueAvailable()) {
                $value = $parameter->getDefaultValue();
            } else {
                throw new InvalidParameterException('Invalid parameters for make()!', 0, null, [
                    'name'       => $name,
                    'accessor'   => static::getFacadeAccessor(),
                    'parameters' => $parameters,
                    'arguments'  => $arguments,
                ]);
            }
            $result[$parameter->getPosition()] = $value;
        }

        return $result ?? [];
    }
}
