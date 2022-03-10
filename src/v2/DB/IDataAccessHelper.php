<?php

namespace Brezgalov\ApiHelpers\v2\DB;

interface IDataAccessHelper
{
    /**
     * @return string
     */
    public static function getPrimaryKeyName();

    /**
     * @return string
     */
    public static function getTable();

    /**
     * @return mixed
     */
    public static function query();

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