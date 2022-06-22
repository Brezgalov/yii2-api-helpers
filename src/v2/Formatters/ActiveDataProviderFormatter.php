<?php

namespace Brezgalov\ApiHelpers\v2\Formatters;

use Brezgalov\ApiHelpers\v2\Formatters\ModelResultFormatter;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

class ActiveDataProviderFormatter extends ModelResultFormatter
{
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
     * @param object $service
     * @param mixed $result
     * @return mixed|object|\yii\base\Model|\yii\web\Response
     * @throws \Exception
     */
    public function format($service, $result)
    {
        if (!($result instanceof ActiveQuery)) {
            return parent::format($service, $result);
        }

        $dataProviderSetup = is_array($this->dataProviderSetup) ? $this->dataProviderSetup : ['class' => $this->dataProviderSetup];
        $dataProviderSetup['query'] = $result;

        $dataProvider = \Yii::createObject($dataProviderSetup);

        if (is_callable($this->afterDataProviderInit)) {
            $dataProvider = call_user_func($this->afterDataProviderInit, $dataProvider);
        }

        return $dataProvider;
    }
}