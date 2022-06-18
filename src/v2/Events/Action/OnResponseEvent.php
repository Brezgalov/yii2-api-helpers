<?php

namespace Brezgalov\ApiHelpers\v2\Events\Action;

use \yii\base\Event;

class OnResponseEvent extends Event
{
    /**
     * @var bool
     */
    public $isFail;

    /**
     * @var object
     */
    public $service;

    /**
     * @var mixed
     */
    public $result;

    /**
     * @var mixed
     */
    public $resultFormatted;
}