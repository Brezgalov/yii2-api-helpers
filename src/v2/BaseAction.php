<?php

namespace Brezgalov\ApiHelpers\v2;

use Brezgalov\ApiHelpers\v2\Events\Action\OnBeforeMethodEvent;
use Brezgalov\ApiHelpers\v2\Events\Action\OnExceptionEvent;
use Brezgalov\ApiHelpers\v2\Events\Action\OnResponseEvent;
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
     * @var string
     */
    public $beforeMethodEventConfig = OnBeforeMethodEvent::class;

    /**
     * @var string
     */
    public $onExceptionEventConfig = OnExceptionEvent::class;

    /**
     * @var string
     */
    public $onResponseFailEventConfig = OnResponseEvent::class;

    /**
     * @var string
     */
    public $onResponseSuccessEventConfig = OnResponseEvent::class;

    /**
     * Action constructor.
     * @param string $id
     * @param $controller
     * @param array $config
     */
    public function __construct($id, $controller, $config = [])
    {
        parent::__construct($id, $controller, $config);

        $this->attachBehaviors(
            array_filter(
                array_merge($this->getDefaultBehaviors(), $this->behaviors)
            )
        );
    }

    /**
     * @return array
     */
    protected function getDefaultBehaviors()
    {
        return [];
    }

    /**
     * @param object $service
     */
    public function beforeMethod(&$service)
    {
        $beforeMethodEvent = Yii::createObject($this->beforeMethodEventConfig, [
            'action' => $this,
            'config' => [
                'service' => &$service,
            ],
        ]);

        $this->trigger(self::EVENT_BEFORE_METHOD, $beforeMethodEvent);
    }

    /**
     * @param \Exception $ex
     */
    public function onException($ex)
    {
        $exEvent = Yii::createObject($this->onExceptionEventConfig, [
            'action' => $this,
            'config' => [
                'ex' => $ex,
            ],
        ]);

        $this->trigger(self::EVENT_ON_EXCEPTION, $exEvent);
    }

    /**
     * @param object $service
     * @param mixed $result
     * @param mixed $resultFormatted
     */
    public function onFail($service, $result, $resultFormatted)
    {
        $failEvent = Yii::createObject($this->onResponseFailEventConfig, [
            'action' => $this,
            'config' => [
                'isFail' => true,
                'service' => $service,
                'result' => $result,
                'resultFormatted' => $resultFormatted,
            ],
        ]);

        $this->trigger(self::EVENT_ON_FAIL, $failEvent);
    }

    /**
     * @param object $service
     * @param mixed $result
     * @param mixed $resultFormatted
     */
    public function onSuccess($service, $result, $resultFormatted)
    {
        $successEvent = Yii::createObject($this->onResponseSuccessEventConfig, [
            'action' => $this,
            'config' => [
                'isFail' => false,
                'service' => $service,
                'result' => $result,
                'resultFormatted' => $resultFormatted,
            ],
        ]);

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

        $this->beforeMethod($service);
        try {
            if ($service instanceof IRegisterInput || $service instanceof IRegisterInputInterface) {
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

        $fail = $this->resultIsFailure($result);

        $resultFormatted = $result;
        if ($formatter instanceof IFormatter) {
            $resultFormatted = $formatter->format($service, $result);
        }

        if ($fail) {
            $this->onFail($service, $result, $resultFormatted);
        } else {
            $this->onSuccess($service, $result, $resultFormatted);
        }

        return $resultFormatted;
    }
}