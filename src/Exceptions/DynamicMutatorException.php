<?php

namespace Mindtwo\DynamicMutators\Exceptions;

use Exception;
use Throwable;

class DynamicMutatorException extends Exception
{
    /**
     * Create a new dynamic mutator exception instance.
     *
     * In addition to the usual exception arguments, an associative "dump"
     * array may be supplied. Its contents are appended to the exception
     * message so misconfigurations can be diagnosed from the stack trace
     * alone without needing to inspect a debugger.
     *
     * @param  array<string, mixed>  $dump
     * @return void
     */
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null, array $dump = [])
    {
        $message = $dump ? $message.$this->dumpToString($dump) : $message;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Convert the given dump array into a printable, padded string.
     *
     * Scalar values are appended verbatim while structured values are encoded
     * as JSON so the dump remains a single readable block of text.
     *
     * @param  array<string, mixed>  $dump
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
