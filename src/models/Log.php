<?php

namespace JCIT\Yii2\ActiveRecordLog\models;

use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * Class Log
 * @package JCIT\Yii2\ActiveRecordLog\models
 * @property int $id
 * @property string $model_class
 * @property string $model_id
 * @property string $event
 * @property string $current_attributes
 * @property string $old_attributes
 * @property string $user_id
 * @property string $created
 */
class Log extends ActiveRecord
{
    public function behaviors()
    {
        return [
            BlameableBehavior::class => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'user_id'
            ],
            TimestampBehavior::class => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created']
                ],
                'value' => new Expression('NOW()'),
            ]
        ];
    }

    public static function tableName()
    {
        return '{{%log}}';
    }
}