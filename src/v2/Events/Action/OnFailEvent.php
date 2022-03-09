<?php

namespace Brezgalov\ApiHelpers\v2\Events\Action;

use \yii\base\Event;

class OnFailEvent extends Event
{
    /**
     * @var object
     */
    public $service;
}