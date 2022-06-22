<?php

namespace Brezgalov\ApiHelpers\v2;

use Brezgalov\ApiHelpers\v2\Services\SearchAdapterService;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class ApiSearchAction extends ApiGetAction
{
    /**
     * @var array|string
     */
    public $searchModel;

    /**
     * @var string
     */
    public $methodName = SearchAdapterService::SEARCH_METHOD;

    /**
     * @var string
     */
    public $dataProviderClass = ActiveDataProvider::class;

    /**
     * @var array
     */
    public $dataProviderSetup = [];

    /**
     * ApiSearchAction constructor.
     * @param $id
     * @param $controller
     * @param array $config
     */
    public function __construct($id, $controller, $config = [])
    {
        parent::__construct($id, $controller, $config);

        $this->service = [
            'class' => SearchAdapterService::class,
            'searchModel' => $this->searchModel,
            'dataProviderSetup' => $this->getDataProviderSetup(),
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