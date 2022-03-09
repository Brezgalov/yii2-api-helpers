<?php

namespace Brezgalov\ApiHelpers\v2;

class ErrorException extends \Exception
{
    /**
     * @var int
     */
    public $statusCode = 400;

    /**
     * @var mixed
     */
    public $error;

    /**
     * @param string $error
     * @param string $statusCode
     * @param string $errorName
     * @throws ErrorException
     */
    public static function throw($error, $statusCode)
    {
        $ex = new static();
        $ex->statusCode = $statusCode;
        $ex->error = $error;

        throw $ex;
    }

    /**
     * @param $attribute
     * @param $error
     * @throws ErrorException
     */
    public static function throwAsModelError($attribute, $error)
    {
        $ex = new static();
        $ex->statusCode = 422;
        $ex->error = [
            [
                "field" => $attribute,
                "message" => $error,
            ],
        ];

        throw $ex;
    }
}