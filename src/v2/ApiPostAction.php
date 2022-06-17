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
    /**
     * @var IFormatter
     */
    public $formatter = ModelResultFormatter::class;

    /**
     * @var string[]
     */
    public $behaviors = [
        TransactionBehavior::class,
        MutexBehavior::class,
        DelayedEventsBehavior::class,
    ];
}