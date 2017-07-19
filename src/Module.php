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

        foreach ($classes as $class => $classConfig) {
            if (is_string($classConfig)) {
                $class = $classConfig;
                $classConfig = $this->defaultClassConfig;
            }

            $readEvents = ArrayHelper::getValue($classConfig, 'readEvents', []);
            foreach ($readEvents as $event => $eventConfig) {
                if (is_string($eventConfig)) {
                   $event = $eventConfig;
                   $eventConfig = [];
                }

                $eventConfig['logModelClass'] = $eventConfig['logModelClass'] ?? $classConfig['logModelClass'];

                Event::on($class, $event, function(Event $event) use ($eventConfig) {
                    $this->logRead($event->sender, $eventConfig, $event);
                });
            }

            $updateEvents = ArrayHelper::getValue($classConfig, 'updateEvents', []);
            foreach ($updateEvents as $event => $eventConfig) {
                if (is_string($eventConfig)) {
                    $event = $eventConfig;
                    $eventConfig = [];
                }

                $eventConfig['logModelClass'] = $eventConfig['logModelClass'] ?? $classConfig['logModelClass'];

                Event::on($class, $event, function(Event $event) use ($eventConfig) {
                    $this->logUpdate($event->sender, $eventConfig, $event);
                });
            }
        }
    }

    public function logRead(ActiveRecord $model, $eventConfig = [], $event = null)
    {
        $log = $this->createLog($model, $eventConfig, $event);

        return $this->saveLog($log);
    }

    public function logUpdate(ActiveRecord $model, $eventConfig = [], $event = null)
    {
        $log = $this->createLog($model, $eventConfig, $event);
        $log->old_attributes = $model->oldAttributes;
        return $this->saveLog($log);
    }

    /**
     * @param $eventConfig
     * @return Log
     */
    protected function createLog(ActiveRecord $model, $eventConfig = [], $event = null)
    {
        $logModelClass = ArrayHelper::getValue($eventConfig, 'modelClass', $this->logModelClass);
        /** @var Log $log */
        $log = new $logModelClass();
        $log->model_class = get_class($model);
        $log->model_id = $model->getPrimaryKey();
        $log->event = $event instanceof Event ? $event->name : $event;
        $log->current_attributes = json_encode($model->attributes);
        return $log;
    }

    protected function saveLog(Log $log)
    {
        $result = true;
        //trigger before save to make sure behaviors are triggered (needed when saving in batches)
        if ($result = $result && $log->beforeSave(true)) {
            $result = $result && $log->save();
        }
        return $result;
    }
}