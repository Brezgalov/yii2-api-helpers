<?php

namespace Brezgalov\ApiHelpers\v2;

use Brezgalov\ApiHelpers\v2\Formatters\ActiveDataProviderFormatter;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class ApiActiveGetAction extends ApiGetAction
{
    /**
     * @var string
     */
    public $methodName = 'getQuery';

    /**
     * @var string
     */
    public $dataProviderClass = ActiveDataProvider::class;

    /**
     * @var array
     */
    public $dataProviderSetup = [];

    /**
     * @var callable
     */
    public $afterDataProviderInit;

    /**
     * ApiSearchAction constructor.
     * @param $id
     * @param $controller
     * @param array $config
     */
    public function __construct($id, $controller, $config = [])
    {
        parent::__construct($id, $controller, $config);

        $this->formatter = [
            'class' => ActiveDataProviderFormatter::class,
            'dataProviderSetup' => $this->getDataProviderSetup(),
            'afterDataProviderInit' => $this->afterDataProviderInit,
        ];
    }

    /**
     * @return array|string
     */
    protected function getDataProviderSetup()
    {
        if (empty($this->dataProviderSetup)) {
            return $this->dataProviderClass;
        }

        return ArrayHelper::merge(
            ['class' => $this->dataProviderClass],
            $this->dataProviderSetup
        );
    }
}