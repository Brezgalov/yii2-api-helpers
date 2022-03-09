<?php

namespace Brezgalov\ApiHelpers\v2\Behaviors\Action;

use Brezgalov\ApiHelpers\v2\Action;
use Brezgalov\ApiHelpers\v2\Events\DelayedEventsStorage;
use yii\base\Behavior;
use yii\base\InvalidConfigException;

class DelayedEventsBehavior extends Behavior
{
    /**
     * @var string
     */
    public $componentName = "DES";

    /**
     * @return DelayedEventsStorage
     * @throws InvalidConfigException
     */
    public function getStore()
    {
        if (!\Yii::$app->has($this->componentName)) {
            throw new InvalidConfigException("Component \"{$this->componentName}\" can not be found in app");
        }

        return \Yii::$app->get($this->componentName);
    }

    /**
     * @return string[]
     */
    public function events()
    {
        return [
            Action::EVENT_ON_SUCCESS => 'flush',
        ];
    }

    /**
     * @throws InvalidConfigException
     */
    public function flush()
    {
        $this->getStore()->fireEvents();
    }
}