<?php

namespace Brezgalov\ApiHelpers\v2\DB\DTO;

abstract class BaseDTO implements IDTO
{
    /**
     * @var int
     */
    public $id;

    /**
     * @return bool
     */
    public function isNew()
    {
        return empty($this->id);
    }

    /**
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $value
     * @return bool
     */
    public function setId($value)
    {
        $this->id = intval($value);

        return true;
    }

    /**
     * @param array $data
     * @return bool|void
     */
    public function loadFromDbData(array $data)
    {
        $this->id = (int)$data['id'];
    }

    /**
     * @return int[]
     */
    public function toArray()
    {
        return ['id' => $this->id];
    }
}