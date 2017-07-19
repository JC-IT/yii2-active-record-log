<?php

namespace JCIT\Yii2\ActiveRecordLog\models;

use yii\db\ActiveRecord;

/**
 * Class Log
 * @package JCIT\Yii2\ActiveRecordLog\models
 * @property int $id
 * @property string $
 */
class Log extends ActiveRecord
{
    protected static $table;
    protected static $db;

    public static function tableName()
    {
        return static::tableName();
    }

    public static function setDB($db)
    {
        static::$db = $db;
    }

    public static function setTableName($table)
    {
        static::$table = $table;
    }
}