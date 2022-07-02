<?php

namespace example;

use Brezgalov\ApiHelpers\v2\IRegisterInput;
use yii\base\Model;

class MyExampleRepositoryService extends Model implements IRegisterInput
{
    const METHOD_LIST = 'listData';

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @return array[]
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name'], 'string'],
        ];
    }

    /**
     * @param array $data
     */
    public function registerInput(array $data = [])
    {
        $this->name = $data['name'] ?? $this->name;
        $this->id = $data['id'] ?? $this->name;
    }

    /**
     * @return array[]
     */
    protected function getExampleData()
    {
        return [
            [
                'id' => 1,
                'name' => 'Barbara',
                'sex' => 'female',
            ],
            [
                'id' => 2,
                'name' => 'Mike',
                'sex' => 'male',
            ],
        ];
    }

    /**
     * @return array[]
     */
    public function listData()
    {
        return array_filter($this->getExampleData(), function($item) {
            $nameMatch = !is_null($this->name) ? strcmp($item['name'], $this->name) !== 0 : true;
            $idMatch = !is_null($this->id) ? $item['id'] !== $this->id : true;

            return $idMatch && $nameMatch;
        });
    }

    public function getTime()
    {
        return date(DATETIME_FORMAT);
    }
}