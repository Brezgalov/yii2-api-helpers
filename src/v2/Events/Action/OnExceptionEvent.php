<?php

namespace Brezgalov\ApiHelpers\v2\Events\Action;

use \yii\base\Event;

class OnExceptionEvent extends Event
{
    /**
     * @var \Exception
     */
    public $ex;
}