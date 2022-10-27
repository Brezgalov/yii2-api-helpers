<?php

namespace Brezgalov\ApiHelpers\v2;

use Brezgalov\ApiHelpers\v2\Behaviors\Action\DelayedEventsBehavior;
use Brezgalov\ApiHelpers\v2\Behaviors\Action\MutexBehavior;

class ApiNoTransactionPostAction extends ApiPostAction
{
    /**
     * @return string[]
     */
    protected function getDefaultBehaviors()
    {
        return [
            static::BEHAVIOR_KEY_MUTEX => MutexBehavior::class,
            static::BEHAVIOR_KEY_DELAYED_EVENTS => DelayedEventsBehavior::class,
        ];
    }
}