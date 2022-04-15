<?php

namespace Brezgalov\ApiHelpers\v2\Behaviors\Action;

use Brezgalov\ApiHelpers\v2\BaseAction;
use Brezgalov\ApiHelpers\v2\Events\DelayedEventsStorage;
use yii\base\Behavior;
use yii\base\InvalidConfigException;

class DelayedEventsBehavior extends Behavior
{
    /**
     * @return string[]
     */
    public function events()
    {
        return [
            BaseAction::EVENT_ON_SUCCESS => 'flush',
        ];
    }

    /**
     * @throws InvalidConfigException
     */
    public function flush()
    {
        DelayedEventsStorage::fireEvents();
    }
}