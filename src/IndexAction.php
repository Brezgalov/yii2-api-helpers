<?php

namespace Brezgalov\ApiHelpers;

use Yii;
use Brezgalov\ApiHelpers\ISearch;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\web\ServerErrorHttpException;

/**
 * Class IndexAction
 * @package common\actions
 */
class IndexAction extends \yii\rest\IndexAction
{
    /**
     * @var string
     */
    public $modelClass = '';

    /**
     * @var array
     */
    public $defaultOrder = ['id' => 'DESC'];

    /**
     * @var string
     */
    public $dataProviderClass = ActiveDataProvider::class;

    /**
     * @var string
     */
    public $searchClass;

    /**
     * @var bool
     */
    public $paginationActive = true;

    /**
     * @return object|ActiveDataProvider
     * @throws ServerErrorHttpException
     * @throws InvalidConfigException
     */
    protected function prepareDataProvider()
    {
        $requestParams = array_merge(Yii::$app->getRequest()->getQueryParams(), Yii::$app->getRequest()->getBodyParams());

        $classPropertiesRequired = ['dataProviderClass', 'searchClass'];
        foreach ($classPropertiesRequired as $property) {
            if (
                !isset($this->{$property}) ||
                is_string($this->{$property}) && !class_exists($this->{$property})) {
                throw new ServerErrorHttpException("У экшена не указано свойство {$property}");
            }
        }

        $search = $this->prepareSearchModel($requestParams);
        if (!$search->validate()) {
            \Yii::$app->response->statusCode = 422;
            return $search;
        }

        $paging = false;
        if ($this->paginationActive) {
            $paging = [
                'params' => $requestParams,
            ];
        }

        return Yii::createObject([
            'class' => $this->dataProviderClass,
            'query' => $search->getQuery(),
            'pagination' => $paging,
            'sort' => [
                'params' => $requestParams,
                'defaultOrder' => $this->defaultOrder,
            ],
        ]);
    }

    /**
     * @param array $requestParams
     * @return ISearch
     * @throws InvalidConfigException
     */
    protected function prepareSearchModel($requestParams = [])
    {
        $search = null;

        if (is_string($this->searchClass)) {
            $search = Yii::createObject($this->searchClass);
        } elseif ($this->searchClass instanceof ISearch) {
            $search = $this->searchClass;
        }

        if (empty($search)) {
            throw new \Exception('Не могу определить модель для поиска');
        }

        if (!($search instanceof ISearch)) {
            throw new \Exception('Модель для поиска должна реализовывать ISearch');
        }

        $search->load($requestParams, '');

        return $search;
    }
}