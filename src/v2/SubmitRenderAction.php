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
    /**
     * @var string
     */
    public $successRedirectRoute;

    /**
     * @var array
     */
    public $behaviors = [
        TransactionBehavior::class,
        MutexBehavior::class,
        DelayedEventsBehavior::class,
    ];

    /**
     * CreateRoleSubmitFormAction constructor.
     * @param $id
     * @param $controller
     * @param array $config
     */
    public function __construct($id, $controller, $config = [])
    {
        $this->formatter = [
            'class' => RenderOrRedirectFormatter::class,
            'redirectUrl' => Url::toRoute($this->successRedirectRoute),
        ];

        parent::__construct($id, $controller, $config);
    }
}