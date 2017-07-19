<?php

namespace JCIT\Yii2\ActiveRecordLog;

use JCIT\Yii2\ActiveRecordLog\models\Log;
use yii\base\Event;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class Module extends \yii\base\Module
{
    public $eventClass = ActiveRecord::class;

    public $logModelClass = Log::class;

    public $logClasses = [];
    public $defaultClassConfig = [
        'readEvents' => [
            ActiveRecord::EVENT_AFTER_FIND
        ],
        'updateEvents' => [
            ActiveRecord::EVENT_AFTER_INSERT,
            ActiveRecord::EVENT_AFTER_UPDATE
        ]
    ];

    public function init()
    {
        if (empty($this->logClasses)) {
            $classes = [ActiveRecord::class];
        } else {
            $classes = $this->logClasses;
        }

        foreach ($classes as $class => $config) {
            if (is_string($config)) {
                $class = $config;
                $config = $this->defaultClassConfig;
            }

            $readEvents = ArrayHelper::getValue($config, 'readEvents', []);
            foreach ($readEvents as $event => $eventConfig) {
                if (is_string($eventConfig)) {
                   $event = $eventConfig;
                   $eventConfig = [];
                }

                Event::on($class, $event, function(Event $event) use ($eventConfig) {
                    $this->logRead($event->sender, $eventConfig, $event);
                });
            }

            $updateEvents = ArrayHelper::getValue($config, 'updateEvents', []);
            foreach ($updateEvents as $event => $eventConfig) {
                if (is_string($eventConfig)) {
                    $event = $eventConfig;
                    $eventConfig = [];
                }

                Event::on($class, $event, function(Event $event) use ($eventConfig) {
                    $this->logUpdate($event->sender, $eventConfig, $event);
                });
            }
        }
    }

    public function logRead($model, $eventConfig = [], $event = null)
    {
        vdd($eventConfig);
    }

    public function logUpdate($model, $eventConfig = [], $event = null)
    {

    }
}