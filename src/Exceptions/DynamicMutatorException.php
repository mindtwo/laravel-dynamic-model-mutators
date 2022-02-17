<?php

namespace Mindtwo\DynamicMutators\Exceptions;

use Exception;
use Throwable;

class DynamicMutatorException extends Exception
{
    /**
     * DynamicMutatorException constructor.
     *
     * @param  string  $message
     * @param  int  $code
     * @param  Throwable|null  $previous
     * @param  array  $dump
     */
    public function __construct(string $message = '', int $code = 0, Throwable $previous = null, array $dump = [])
    {
        $message = $dump ? $message.$this->dumpToString($dump) : $message;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Convert dump array to string.
     *
     * @param  array  $dump
     * @return string
     */
    protected function dumpToString(array $dump): string
    {
        return collect($dump)
            ->map(function ($item, $key) {
                return str_pad($key.':', 15, ' ')
                       .(is_scalar($item) ? $item : json_encode($item));
            })
            ->prepend("\n")
            ->implode("\n");
    }
}
