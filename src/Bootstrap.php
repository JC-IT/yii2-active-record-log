<?php

namespace JCIT\Yii2\ActiveRecordLog;

use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\helpers\ArrayHelper;

class Bootstrap implements BootstrapInterface
{
    /**
     * @param Application $app
     */
    public function bootstrap($app)
    {
        foreach ($app->getModules() as $key => $config) {
            $class = is_string($config) ? $config : ArrayHelper::getValue($config, 'class');
            if (
                $class == Module::class || is_subclass_of($class, Module::class)
            ) {
                $app->bootstrap[] = $key;
            }
        }
    }
}