<?php

namespace Brezgalov\ApiHelpers\v2\Events\Action;

use yii\base\Action;
use \yii\base\Event;

abstract class BaseActionEvent extends Event
{
    /**
     * @var Action
     */
    public $action;

    /**
     * BaseActionEvent constructor.
     * @param Action $action
     * @param array $config
     */
    public function __construct(Action $action, $config = [])
    {
        $this->action = $action;
        parent::__construct($config);
    }
}