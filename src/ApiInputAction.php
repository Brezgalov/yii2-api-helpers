<?php

namespace Brezgalov\ApiHelpers;

use Brezgalov\ApiHelpers\IApiInputForm;
use Brezgalov\ApiHelpers\SendToOutputException;
use Yii;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\db\Exception;
use yii\di\NotInstantiableException;
use yii\mutex\Mutex;
use yii\web\ServerErrorHttpException;

/**
 * Class ApiInputAction
 * @package app\actions
 */
class ApiInputAction extends Action
{
    /**
     * @var string
     */
    public $unknownValidationErrorText = 'Unknown error 1';

    /**
     * @var string
     */
    public $unknownExecutionErrorText = 'Unknown error 2';

    /**
     * @var string
     */
    public $modelClass;

    /**
     * @var
     */
    public $checkAccess;

    /**
     * @var array
     */
    public $formConstructParams = [];

    /**
     * @var Mutex
     */
    public $mutexComp;

    /**
     * @var IApiMutexHelper
     */
    public $mutexHelper;

    /**
     * @var bool
     */
    public $useMutex = true;

    /**
     * seconds before mutex exception
     * @var int
     */
    public $mutexTimeout = 30;

    /**
     * @var string
     */
    private $shouldRelease;

    /**
     * ApiInputAction constructor.
     * @param $id
     * @param $controller
     * @param array $config
     * @throws InvalidConfigException
     */
    public function __construct($id, $controller, $config = [])
    {
        parent::__construct($id, $controller, $config);

        if (empty($this->mutexComp) && \Yii::$app->has('mutex')) {
            $this->mutexComp = \Yii::$app->get('mutex');
        }

        if (empty($this->mutexHelper)) {
            $this->mutexHelper = new ApiMutexHelper();
        }
    }

    /**
     * lock action if we can
     * @return bool
     */
    protected function acquireMutex()
    {
        if (!$this->useMutex || empty($this->mutexHelper) || empty($this->mutexComp)) {
            return true;
        }

        $this->shouldRelease = $this->mutexHelper->buildActionMutexName();

        if (!$this->mutexComp->acquire($this->shouldRelease, $this->mutexTimeout)) {
            throw new ServerErrorHttpException("Lock can not be acquired after {$this->mutexTimeout} seconds");
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function releaseMutex()
    {
        if ($this->shouldRelease && $this->mutexComp) {
            $this->mutexComp->release($this->shouldRelease);
            $this->shouldRelease = null;
        }

        return true;
    }

    /**
     * @return IApiInputForm|false[]|mixed|Model
     * @throws Exception
     * @throws InvalidConfigException
     * @throws NotInstantiableException
     */
    public function run()
    {
        // Mutex предотвращает атаки и баги связанные с выполнением двух миграций параллельно
        $this->acquireMutex();

        $trans = Yii::$app->db->beginTransaction();

        try {
            $result = $this->_run();
        } catch (SendToOutputException $resultEx) {
            if ($resultEx->commitTransaction) {
                $trans->commit();
            } else {
                $trans->rollBack();
            }

            Yii::$app->response->setStatusCode($resultEx->statusCode ?: 406);
            return $resultEx->response ?: ['success' => false];
        } catch (\Exception $ex) {
            $trans->rollBack();

            throw $ex;
        }

        if ($result instanceof Model && $result->hasErrors()) {
            $trans->rollBack();
        } else {
            $trans->commit();
        }

        $this->releaseMutex();

        return $result;
    }

    /**
     * @return IApiInputForm|mixed
     * @throws InvalidConfigException
     * @throws NotInstantiableException
     */
    protected function _run()
    {
        $input = array_merge(
            Yii::$app->request->getBodyParams(),
            Yii::$app->request->getQueryParams()
        );

        /* @var $form IApiInputForm */
        if (is_string($this->modelClass) || is_array($this->modelClass)) {
            $form = Yii::createObject($this->modelClass);
        } elseif ($this->modelClass instanceof IApiInputForm) {
            $form = $this->modelClass;
        } else {
            throw new InvalidConfigException('Не удается создать форму ввода для экшена');
        }

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $form);
        }

        $form->load($input, '');
        if (!$form->validate()) {
            if (!$form->hasErrors()) {
                $form->addError('class', $this->unknownValidationErrorText);
            }

            return $form;
        }

        $result = $form->getResult();
        if ($result === false) {
            if (!$form->hasErrors()) {
                $form->addError('class', $this->unknownExecutionErrorText);
            }

            return $form;
        }

        return $result;
    }
}
