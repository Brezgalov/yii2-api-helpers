<?php

namespace Brezgalov\ApiHelpers\v2\Helpers;

abstract class ObjectEmptyFieldsFinder
{
    /**
     * @param object $object
     * @param array $fieldsToSearch
     * @return array
     */
    public static function findEmptyFields(object $object, array $fieldsToSearch): array
    {
        $empty = [];

        foreach ($fieldsToSearch as $alias => $field) {
            if (empty($object->{$field})) {
                $empty[] = is_string($alias) ? $alias : $field;
            }
        }

        return $empty;
    }
}