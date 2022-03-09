<?php

namespace Brezgalov\ApiHelpers\v2\Behaviors\Action;

use Brezgalov\ApiHelpers\ApiMutexHelper;
use Brezgalov\ApiHelpers\v2\Action;
use yii\base\Behavior;
use yii\base\ErrorException;
use yii\mutex\Mutex;
use Yii;

class MutexBehavior extends Behavior
{
    /**
     * @var Mutex
     */
    public $mutexComp;

    /**
     * seconds before mutex exception
     * @var int
     */
    public $mutexTimeout = 30;

    /**
     * @var ApiMutexHelper
     */
    public $mutexHelper;

    /**
     * @var string
     */
    protected $mutexName;

    /**
     * MutexBehavior constructor.
     * @param array $config
     * @throws \yii\base\InvalidConfigException
     */
    public function __construct($config = [])
    {
        $this->mutexHelper = new ApiMutexHelper();

        parent::__construct($config);

        if (empty($this->mutexComp) && Yii::$app->has('mutex')) {
            $this->mutexComp = Yii::$app->get('mutex');
        }
    }

    /**
     * @return string[]
     */
    public function events()
    {
        return [
            Action::EVENT_BEFORE_METHOD => 'aquire',
            Action::EVENT_ON_SUCCESS => 'release',
            Action::EVENT_ON_FAIL => 'release',
            Action::EVENT_ON_EXCEPTION => 'release',
        ];
    }

    /**
     * @throws ErrorException
     */
    public function aquire()
    {
        $this->mutexName = $this->mutexHelper->buildActionMutexName();

        if (!$this->mutexComp->acquire($this->shouldRelease, $this->mutexTimeout)) {
            throw new ErrorException("Lock can not be acquired after {$this->mutexTimeout} seconds");
        }
    }

    /**
     * void
     */
    public function release()
    {
        $this->mutexComp->release($this->mutexName);
        $this->mutexName = null;
    }
}