<?php

namespace Brezgalov\ApiHelpers\v2\DB;

interface IDataAccessHelper
{
    /**
     * @deprecated
     * @return string
     */
    public function getPrimaryKeyName();

    /**
     * @deprecated
     * @return string
     */
    public function getTable();

    /**
     * @deprecated
     * @return mixed
     */
    public function query();

    /**
     * @param array $columns
     * @return array|false|mixed
     * @throws \Exception
     */
    public function insert(array $columns);

    /**
     * @param $condition - можно передать id как есть, он превратится в ['id' => $condition]
     * @param array $columns
     * @return int
     */
    public function update($condition, array $columns);

    /**
     * @param array|string|null $condition
     * @return int
     * @throws \yii\db\Exception
     */
    public function delete($condition = null);
}