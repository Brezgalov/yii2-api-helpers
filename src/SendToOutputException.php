<?php

namespace Brezgalov\ApiHelpers;

use yii\base\Exception;

class SendToOutputException extends Exception
{
    /**
     * @var int
     */
    public $statusCode = 400;

    /**
     * @var mixed
     */
    public $response;

    /**
     * @var bool
     */
    public $commitTransaction = false;

    /**
     * @param $response
     * @param false $commit
     * @throws SendToOutputException
     */
    public static function throwResponse($response, $commit = false, $statusCode = 400)
    {
        $ex = new static();
        $ex->response = $response;
        $ex->commitTransaction = $commit;
        $ex->statusCode = $statusCode;

        throw $ex;
    }

    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'SendToOutputException';
    }
}