<?php

namespace ostark\Yii2ArtisanBridge;

use ostark\Yii2ArtisanBridge\base\Commands;

class Bridge
{
    public static function registerGroup(ActionGroup $group)
    {
        $handler = new ErrorHandler();
        $handler->register();

        \Yii::$app->set('errorHandler', $handler);

        \Yii::$app->controllerMap[$group->name] = [
            'class'         => Commands::class,
            'actions'       => $group->getActions(),
            'optionAliases' => $group->getOptions(),
            'helpSummary'   => $group->getHelpSummary(),
            'defaultAction' => $group->getDefaultAction()
        ];

    }
}
