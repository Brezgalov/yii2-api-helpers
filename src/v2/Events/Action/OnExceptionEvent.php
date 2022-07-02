<?php

namespace Brezgalov\ApiHelpers\v2\Events\Action;

class OnExceptionEvent extends BaseActionEvent
{
    /**
     * @var \Exception
     */
    public $ex;
}