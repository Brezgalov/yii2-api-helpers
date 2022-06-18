<?php

namespace Brezgalov\ApiHelpers\v2\Events\Action;

use \yii\base\Event;

class OnSuccessEvent extends Event
{
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