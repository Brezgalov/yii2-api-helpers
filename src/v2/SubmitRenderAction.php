<?php

namespace Brezgalov\ApiHelpers\v2;

use Brezgalov\ApiHelpers\v2\Behaviors\Action\DelayedEventsBehavior;
use Brezgalov\ApiHelpers\v2\Behaviors\Action\MutexBehavior;
use Brezgalov\ApiHelpers\v2\Behaviors\Action\TransactionBehavior;
use Brezgalov\ApiHelpers\v2\Formatters\RenderOrRedirectFormatter;
use yii\base\ViewContextInterface;
use yii\base\Model;
use yii\helpers\Url;

/**
 * Class SubmitRenderAction
 *
 * Действие для подтверждения формы
 * В случае успешного подтверждения произведет редирект
 * В случае ошибки - отрисует форму с выводом ошибок
 * Использует набор поведений для изменения состояния системы
 *
 * @package Brezgalov\ApiHelpers\v2
 */
class SubmitRenderAction extends RenderAction
{
    const BEHAVIOR_KEY_TRANSACTION = 'transaction';
    const BEHAVIOR_KEY_MUTEX = 'mutex';
    const BEHAVIOR_KEY_DELAYED_EVENTS = 'delayedEvents';

    /**
     * @var string
     */
    public $successRedirectRoute;

    /**
     * CreateRoleSubmitFormAction constructor.
     * @param $id
     * @param $controller
     * @param array $config
     */
    public function __construct($id, $controller, $config = [])
    {
        parent::__construct($id, $controller, $config);

        if (empty($this->formatter)) {
            $this->formatter = [
                'class' => RenderOrRedirectFormatter::class,
                'redirectUrl' => Url::toRoute($this->successRedirectRoute),
            ];
        }
    }

    /**
     * @return array
     */
    protected function getDefaultBehaviors()
    {
        return [
            static::BEHAVIOR_KEY_TRANSACTION => TransactionBehavior::class,
            static::BEHAVIOR_KEY_MUTEX => MutexBehavior::class,
            static::BEHAVIOR_KEY_DELAYED_EVENTS => DelayedEventsBehavior::class,
        ];
    }
}