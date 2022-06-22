<?php

namespace Brezgalov\ApiHelpers\v2\Services;

use Brezgalov\ApiHelpers\ISearch;
use Brezgalov\ApiHelpers\v2\IRegisterInputInterface;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\DataProviderInterface;

class SearchAdapterService extends Model implements IRegisterInputInterface
{
    const SEARCH_METHOD = 'search';

    /**
     * @var array|string|ISearch
     */
    public $searchModel;

    /**
     * @var
     */
    protected $searchModelInited;

    /**
     * Массив с настройками для инициализации объекта DataProvider
     * @var array
     */
    public $dataProviderSetup = [
        'class' => ActiveDataProvider::class,
    ];

    /**
     * Callback для редактирования объекта DataProvider
     * @var callable
     */
    public $afterDataProviderInit;

    /**
     * @throws \yii\base\InvalidConfigException
     */
    protected function initSearchModel()
    {
        if ($this->searchModelInited) {
            return;
        }

        $this->searchModelInited = \Yii::createObject($this->searchModel);
    }

    /**
     * @param array $data
     */
    public function registerInput(array $data = [])
    {
        $this->initSearchModel();

        if ($this->searchModelInited instanceof IRegisterInputInterface) {
            $this->searchModelInited->registerInput($data);
        } elseif ($this->searchModelInited instanceof Model) {
            $this->searchModelInited->load($data, '');
        }
    }

    /**
     * @return DataProviderInterface|false
     * @throws \yii\base\InvalidConfigException
     */
    public function search()
    {
        /**
         * На случай если registerInput не был вызван
         */
        $this->initSearchModel();

        if ($this->searchModelInited instanceof Model && !$this->searchModelInited->validate()) {
            $this->addErrors($this->searchModelInited->getErrors());
            return false;
        }

        $dataProviderSetup = is_array($this->dataProviderSetup) ? $this->dataProviderSetup : ['class' => $this->dataProviderSetup];
        $dataProviderSetup['query'] = $this->searchModelInited->getQuery();

        $dataProvider = \Yii::createObject($dataProviderSetup);

        if (is_callable($this->afterDataProviderInit)) {
            $dataProvider = call_user_func($this->afterDataProviderInit, $dataProvider);
        }

        return $dataProvider;
    }
}