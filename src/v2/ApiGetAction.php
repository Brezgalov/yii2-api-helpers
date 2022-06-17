<?php


namespace Brezgalov\ApiHelpers\v2;

use Brezgalov\ApiHelpers\v2\Formatters\ModelResultFormatter;
use yii\base\InvalidConfigException;
use Yii;

/**
 * Class Action
 * Класс представляющий действия исключительно отдающие данные
 * Поведения не нужны, если не делаем действий меняющих состояние системы
 *
 * @package Brezgalov\ApiHelpers\v2
 */
class ApiGetAction extends BaseAction
{
    /**
     * @var IFormatter
     */
    public $formatter = ModelResultFormatter::class;
}