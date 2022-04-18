<?php

namespace Brezgalov\ApiHelpers\v2;

use Brezgalov\ApiHelpers\v2\Events\Action\OnExceptionEvent;
use Brezgalov\ApiHelpers\v2\Events\Action\OnFailEvent;
use Brezgalov\ApiHelpers\v2\Events\Action\OnSuccessEvent;
use yii\base\InvalidConfigException;
use yii\base\Action as BaseActionYii2;
use Yii;

abstract class BaseAction extends BaseActionYii2
{
    const EVENT_BEFORE_METHOD = 'beforeMethod';
    const EVENT_ON_EXCEPTION = 'onException';
    const EVENT_ON_FAIL = 'onFail';
    const EVENT_ON_SUCCESS = 'onSuccess';

    /**
     * @var string|array|object
     */
    public $service;

    /**
     * @var string
     */
    public $methodName;

    /**
     * @var IFormatter
     */
    public $formatter;

    /**
     * @var string[]
     */
    public $behaviors = [];

    /**
     * Action constructor.
     * @param string $id
     * @param $controller
     * @param array $config
     */
    public function __construct($id, $controller, $config = [])
    {
        parent::__construct($id, $controller, $config);

        $this->attachBehaviors($this->behaviors);
    }

    /**
     * void
     */
    public function beforeMethod()
    {
        $this->trigger(self::EVENT_BEFORE_METHOD);
    }

    /**
     * @param \Exception $ex
     */
    public function onException($ex)
    {
        $exEvent = new OnExceptionEvent();
        $exEvent->ex = $ex;

        $this->trigger(self::EVENT_ON_EXCEPTION, $exEvent);
    }

    /**
     * @param object $service
     */
    public function onFail($service)
    {
        $failEvent = new OnFailEvent();
        $failEvent->service = $service;

        $this->trigger(self::EVENT_ON_FAIL, $failEvent);
    }

    /**
     * @param object $service
     * @param mixed $result
     */
    public function onSuccess($service, $result)
    {
        $successEvent = new OnSuccessEvent();
        $successEvent->service = $service;
        $successEvent->result = $result;

        $this->trigger(self::EVENT_ON_SUCCESS, $successEvent);
    }

    /**
     * @return IFormatter|null
     */
    public function getFormatter()
    {
        if (empty($this->formatter)) {
            return null;
        }

        if (is_string($this->formatter) || is_array($this->formatter)) {
            return Yii::createObject($this->formatter);
        } elseif ($this->formatter instanceof IFormatter) {
            return $this->formatter;
        }

        return null;
    }

    /**
     * @param mixed $result
     * @return bool
     */
    protected function resultIsFailure($result)
    {
        return $result instanceof \Exception || $result === false;
    }

    /**
     * @return \Exception|false|mixed
     * @throws \Exception
     */
    public function run()
    {
        $service = $this->service;
        if (is_string($service) || is_array($service)) {
            $service = Yii::createObject($service);
        }

        $methodName = $this->methodName;
        if (empty($methodName) && $service instanceof IApiInputForm) {
            $methodName = 'getResult';
        }

        if (!method_exists($service, $methodName)) {
            throw new InvalidConfigException("{$methodName} is not presented by " . get_class($service));
        }

        $result = null;
        $formatter = $this->getFormatter();

        $this->beforeMethod();
        try {
            if ($service instanceof IRegisterInputInterface) {
                $service->registerInput(array_merge(
                    Yii::$app->request->getBodyParams(),
                    Yii::$app->request->getQueryParams()
                ));
            }

            $result = call_user_func([$service, $methodName]);
        } catch (\Exception $ex) {
            $this->onException($ex);

            if (empty($formatter)) {
                throw $ex;
            }

            $result = $ex;
        }

        if ($this->resultIsFailure($result)) {
            $this->onFail($service);
        } else {
            $this->onSuccess($service, $result);
        }

        return $formatter instanceof IFormatter ? $formatter->format($service, $result) : $result;
    }
}