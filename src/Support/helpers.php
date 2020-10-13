<?php

use Illuminate\Support\Arr;

if (! function_exists('validate_interfaces_implemented')) {
    /**
     * Validate that the entity implements all of the given interfaces.
     *
     * @param  object|string  $entity
     * @param  string[]  $interfaces
     * @return bool
     * 
     * @throws \Exception
     */
    function validate_interfaces_implemented($entity, ...$interfaces): bool
    {
        $interfaces = Arr::flatten($interfaces);
        
        if (! empty($interfaces)
            && ! Arr::has(class_implements($entity), $interfaces)
        ) {
            $message = sprintf(
                '%s must implement the required interfaces: %s.',
                is_string($entity) ? $entity : get_class($entity),
                implode(', ', $interfaces),
            );

            throw new \Exception($message);
        }

        return true;
    }
}
