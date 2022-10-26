<?php

namespace Brezgalov\ApiHelpers\v2\Helpers;

abstract class Trimmer
{
    /**
     * @param array $array
     * @param array|null $keys
     * @param string $characters
     * @return array
     */
    public static function trimArray(array $array, array $keys = null, string $characters = " \t\n\r\0\x0B"): array
    {
        if (empty($keys)) {
            $keys = array_keys($array);
        }

        foreach ($keys as $key) {
            $array[$key] = trim((string)$array[$key], $characters);
        }

        return $array;
    }
}