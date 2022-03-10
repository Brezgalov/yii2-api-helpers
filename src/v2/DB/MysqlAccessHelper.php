<?php

namespace Brezgalov\ApiHelpers\v2\DB;

use yii\db\Connection;
use yii\db\Query;
use yii\helpers\ArrayHelper;

abstract class MysqlAccessHelper implements IDataAccessHelper
{
    /**
     * @return string
     */
    public static function getPrimaryKeyName()
    {
        return 'id';
    }

    /**
     * @return string
     */
    public abstract static function getTable();

    /**
     * @return Query
     */
    public static function query()
    {
        return (new Query())
            ->select('*')
            ->from(static::getTable());
    }

    /**
     * @param array $columns
     * @return array|false|mixed
     * @throws \Exception
     */
    public function insert(array $columns)
    {
        $db = \Yii::$app->db;
        $res = $db->schema->insert(static::getTable(), $columns);

        return is_array($res) ? ArrayHelper::getValue($res, static::getPrimaryKeyName()) : $res;
    }

    /**
     * @param $condition - можно передать id как есть, он превратится в ['id' => $condition]
     * @param array $columns
     * @return int
     */
    public function update($condition, array $columns)
    {
        $db = \Yii::$app->db;

        if (is_integer($condition) || is_string($condition)) {
            $condition = [static::getPrimaryKeyName() => $condition];
        }

        return $db->createCommand()->update(static::getTable(), $columns, $condition)->execute();
    }

    /**
     * @param array|string|null $condition
     * @return int
     * @throws \yii\db\Exception
     */
    public function delete($condition = null)
    {
        $db = \Yii::$app->db;

        if ($condition) {
            return $db->createCommand()->delete(static::getTable(), $condition)->execute();
        } else {
            return $db->createCommand("DELETE FROM " . static::getTable())->execute();
        }
    }
}