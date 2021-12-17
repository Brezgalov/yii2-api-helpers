<?php

namespace Brezgalov\ApiHelpers;

use yii\base\Model;
use yii\mutex\Mutex;
use yii\web\Request;

class ApiMutexHelper extends Model implements IApiMutexHelper
{
    /**
     * @var string
     */
    public $action;

    /**
     * @var string
     */
    public $controller;

    /**
     * @var string
     */
    public $ip;

    /**
     * @var bool
     */
    public $mutexNameWithIp = true;

    /**
     * prepare controller data
     */
    protected function setControllerInfo()
    {
        $controller = \Yii::$app->controller;

        if (empty($this->controller) && $controller) {
            $this->controller = $controller->id;
        }

        if (empty($this->action) && $controller && $controller->action) {
            $this->action = $controller->action->id;
        }
    }

    /**
     * @return string|null
     */
    public function getControllerId()
    {
        $this->setControllerInfo();

        return $this->controller;
    }

    /**
     * @return string|null
     */
    public function getActionId()
    {
        $this->setControllerInfo();

        return $this->action;
    }

    /**
     * @return string|null
     */
    public function getUserIp()
    {
        if ($this->ip) {
            return $this->ip;
        }

        if (\Yii::$app->has('request')) {
            /** @var Request $request */
            $request = \Yii::$app->get('request');

            if ($request) {
                $this->ip = $request->getUserIP();
            }
        }

        return $this->ip;
    }

    /**
     * @return string
     */
    public function buildActionMutexName()
    {
        $mutexName = $this->getControllerId() . '/' . $this->getActionId();

        if ($this->mutexNameWithIp) {
            $ip = $this->getUserIp();
            if ($ip) {
                $mutexName .= '/' . $this->ip;
            }
        }
        
        return $mutexName;
    }
}