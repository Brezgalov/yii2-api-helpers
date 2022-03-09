<?php

namespace Brezgalov\ApiHelpers\v2\DB;

use yii\db\Connection;
use yii\db\Query;
use yii\helpers\ArrayHelper;

abstract class MysqlAccessHelper
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
     * @param Connection|null $db
     * @return array|false|mixed
     * @throws \Exception
     */
    public static function insert(array $columns, Connection $db = null)
    {
        $db = $db ?: \Yii::$app->db;
        $res = $db->schema->insert(static::getTable(), $columns);

        return is_array($res) ? ArrayHelper::getValue($res, static::getPrimaryKeyName()) : $res;
    }

    /**
     * @param $condition - можно передать id как есть, он превратится в ['id' => $condition]
     * @param array $columns
     * @param Connection|null $db
     * @return \yii\db\Command
     */
    public static function update($condition, array $columns, Connection $db = null)
    {
        $db = $db ?: \Yii::$app->db;

        if (is_integer($condition) || is_string($condition)) {
            $condition = [static::getPrimaryKeyName() => $condition];
        }

        return $db->createCommand()->update(static::getTable(), $columns, $condition)->execute();
    }
}