<?php

namespace Brezgalov\ApiHelpers\v2\Events\Action;

class OnResponseEvent extends BaseActionEvent
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