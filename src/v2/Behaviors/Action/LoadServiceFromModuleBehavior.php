<?php

namespace Brezgalov\ApiHelpers\v2\Behaviors\Action;

use Brezgalov\ApiHelpers\v2\BaseAction;
use Brezgalov\ApiHelpers\v2\Events\Action\OnBeforeMethodEvent;
use Brezgalov\ApiHelpers\v2\ILoadFromModule;
use yii\base\Behavior;

class LoadServiceFromModuleBehavior extends Behavior
{
    /**
     * @return array
     */
    public function events()
    {
        return [
            BaseAction::EVENT_BEFORE_METHOD => 'loadService',
        ];
    }

    /**
     * Applies load method to service
     * Service is supposed to be a link, so changes will appear at trigger point
     *
     * @param OnBeforeMethodEvent $event
     */
    public function loadService(OnBeforeMethodEvent $event)
    {
        if (
            $event->service instanceof ILoadFromModule
            && $event->action
            && $event->action->controller
        ) {
            $event->service->loadFromModule($event->action->controller->module);
        }
    }
}