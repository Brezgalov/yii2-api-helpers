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
     * @return IApiInputForm|false[]|mixed|Model
     * @throws Exception
     * @throws InvalidConfigException
     * @throws NotInstantiableException
     */
    public function run()
    {
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
        $form = Yii::$container->get($this->modelClass, $this->formConstructParams);

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
