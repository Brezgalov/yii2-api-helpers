<?php

namespace Brezgalov\ApiHelpers\v2\Behaviors\Action;

use app\forms\MyExampleRepositoryService;
use Brezgalov\ApiHelpers\v2\ApiGetAction;
use Brezgalov\ApiHelpers\v2\BaseAction;
use Brezgalov\ApiHelpers\v2\Events\Action\OnResponseEvent;
use yii\base\Behavior;
use yii\caching\CacheInterface;

/**
 * Class ApiCacheBehavior
 *
 * 'index' => [
 *      'class' => ApiGetAction::class,
 *      'service' => MyExampleRepositoryService::class,
 *      'methodName' => 'getTime',
 *      'behaviors' => [
 *          [
 *              'class' => ApiCacheBehavior::class,
 *              'cacheDuration' => 20,
 *          ]
 *      ],
 *  ],
 *
 * @package Brezgalov\ApiHelpers\v2\Behaviors\Action
 */
class ApiCacheBehavior extends Behavior
{
    /**
     * @var string
     */
    public $cacheClearParam = 'clear_cache';

    /**
     * @var string
     */
    public $cacheKey;

    /**
     * @var int
     */
    public $cacheDuration;

    /**
     * @var bool
     */
    public $cacheFailures = true;

    /**
     * @var CacheInterface
     */
    public $cacheComponent;

    /**
     * @var callable
     */
    public $keyBuilderCallable;

    /**
     * ResponseCacheBehavior constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        parent::__construct($config);

        if (empty($this->cacheComponent) && \Yii::$app->has('cache')) {
            $this->cacheComponent = \Yii::$app->cache;
        }
    }

    /**
     * @return array
     */
    public function events()
    {
        return [
            BaseAction::EVENT_BEFORE_METHOD => 'getCache',
            BaseAction::EVENT_ON_FAIL => 'setCache',
            BaseAction::EVENT_ON_SUCCESS => 'setCache',
        ];
    }

    /**
     * @return string
     */
    public function getCacheKey()
    {
        if ($this->cacheKey) {
            return $this->cacheKey;
        }

        $this->cacheKey = is_callable($this->keyBuilderCallable) ? call_user_func($this->keyBuilderCallable) : $this->buildCacheKey();

        return $this->cacheKey;
    }

    /**
     * @return string
     */
    protected function buildCacheKey()
    {
        $controllerName = \Yii::$app->controller->id;
        $actionName = \Yii::$app->controller->action ? \Yii::$app->controller->action->id : 'undefined';

        $params = \Yii::$app->request->getQueryParams();
        ksort($params);

        return "{$controllerName}/{$actionName}?params=" . serialize($params);
    }

    /**
     * Checks for cache and displays
     */
    public function getCache()
    {
        $cacheKey = $this->getCacheKey();

        if ($this->cacheClearParam && \Yii::$app->request->getQueryParam($this->cacheClearParam)) {
            $this->cacheComponent->delete($cacheKey);
            return;
        }

        if ($this->cacheComponent->exists($cacheKey)) {
            \Yii::$app->response->data = $this->cacheComponent->get($cacheKey);
            \Yii::$app->response->send();
            exit();
        }
    }

    /**
     * @param OnResponseEvent $event
     */
    public function setCache(OnResponseEvent $event)
    {
        if (!$this->cacheFailures && $event->isFail) {
            return;
        }

        $cacheKey = $this->getCacheKey();
        $this->cacheComponent->set($cacheKey, $event->resultFormatted, $this->cacheDuration);
    }
}