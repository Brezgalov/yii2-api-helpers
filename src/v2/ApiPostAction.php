<?php

namespace Brezgalov\ApiHelpers\v2;

use Brezgalov\ApiHelpers\v2\Behaviors\Action\DelayedEventsBehavior;
use Brezgalov\ApiHelpers\v2\Behaviors\Action\MutexBehavior;
use Brezgalov\ApiHelpers\v2\Behaviors\Action\TransactionBehavior;
use Brezgalov\ApiHelpers\v2\Formatters\ModelResultFormatter;
use yii\base\InvalidConfigException;
use Yii;

/**
 * Class ApiPostAction
 * Класс представляющий действия связанные с изменением состояния системы
 *
 * @package Brezgalov\ApiHelpers\v2
 */
class ApiPostAction extends BaseAction
{
    const BEHAVIOR_KEY_TRANSACTION = 'transaction';
    const BEHAVIOR_KEY_MUTEX = 'mutex';
    const BEHAVIOR_KEY_DELAYED_EVENTS = 'delayedEvents';

    /**
     * @var IFormatter
     */
    public $formatter = ModelResultFormatter::class;

    /**
     * @return string[]
     */
    protected function getDefaultBehaviors()
    {
        return [
            static::BEHAVIOR_KEY_TRANSACTION => TransactionBehavior::class,
            static::BEHAVIOR_KEY_MUTEX  => MutexBehavior::class,
            static::BEHAVIOR_KEY_DELAYED_EVENTS  => DelayedEventsBehavior::class,
        ];
    }
}