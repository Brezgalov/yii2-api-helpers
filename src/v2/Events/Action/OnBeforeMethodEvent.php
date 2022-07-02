<?php

namespace Brezgalov\ApiHelpers\v2\Events\Action;

class OnBeforeMethodEvent extends BaseActionEvent
{
    /**
     * @var object
     */
    public $service;
}