<?php

namespace ostark\Yii2ArtisanBridge\base;

use Yii;
use yii\base\ActionEvent;
use yii\console\Controller as BaseConsoleController;
use yii\helpers\Inflector;


/**
 * Copy Craft effortlessly
 */
class Commands extends BaseConsoleController
{

    public $actions = [];

    public $defaultAction;

    public $options = [];

    public $optionAliases = [];

    /**
     * Commands constructor.
     *
     * @param string           $id
     * @param \yii\base\Module $module
     * @param array            $config
     */
    public function __construct(string $id, \yii\base\Module $module, array $config = [])
    {
        parent::__construct($id, $module, $config);

        // EVENT_BEFORE_ACTION
        $this->on(self::EVENT_BEFORE_ACTION, function (ActionEvent $event) {

            // Forward options to Action
            foreach (array_values($this->optionAliases) as $name) {
                if (in_array($name, array_values($this->optionAliases))) {
                    if (isset($event->action->controller->options[$name])) {
                        $event->action->$name = $event->action->controller->options[$name];
                    }
                }
            }

            // Forward $interactive always
            $event->action->interactive = $event->action->controller->interactive;

        });
    }


    /**
     * Setup for actions and options
     *
     * @param string $prefix
     * @param array  $actions
     * @param array  $options
     */
    public static function register(string $prefix, array $actions = [], array $options = [])
    {
        Yii::$app->controllerMap[$prefix] = [
            'class'         => get_called_class(),
            'actions'       => $actions,
            'optionAliases' => $options
        ];
    }

    /**
     * @param string $prefix
     * @param string $name
     */
    public static function setDefaultAction(string $prefix, string $name)
    {
        Yii::$app->controllerMap[$prefix]['defaultAction'] = $name;
    }

    /**
     * @return array
     */
    public function actions()
    {
        return $this->actions;
    }

    public function optionAliases()
    {
        $defaultOptions = ['h' => 'help', 'i' => 'interactive'];

        return array_merge($defaultOptions, $this->optionAliases);
    }

    /**
     * Get options from public Action properties
     */
    public function options($actionID)
    {
        $actionClass   = $this->actions()[$actionID] ?? null;
        $action        = new \ReflectionClass($actionClass);
        $actionOptions = [];

        foreach ($action->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if (in_array($property->getName(), array_values($this->optionAliases()))) {
                $actionOptions[] = $property->getName();
            }
        }

        return $actionOptions;

    }

    /**
     * Options getter
     *
     * @param string $name
     *
     * @return bool|mixed
     */
    public function __get($name)
    {
        return null;
    }

    /**
     * Options setter
     *
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        $this->options[$name] = $value;
    }


    /**
     * Returns the help information for the options for the action.
     *
     * The returned value should be an array. The keys are the option names, and the values are
     * the corresponding help information. Each value must be an array of the following structure:
     *
     * - type: string, the PHP type of this argument.
     * - default: string, the default value of this argument
     * - comment: string, the comment of this argument
     *
     * The default implementation will return the help information extracted from the doc-comment of
     * the properties corresponding to the action options.
     *
     * @param Action $action
     *
     * @return array the help information of the action options
     * @throws \ReflectionException
     */
    public function getActionOptionsHelp($action)
    {
        $optionNames = $this->options($action->id);
        if (empty($optionNames)) {
            return [];
        }

        $class   = new \ReflectionClass($action);
        $options = [];

        foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            $name = $property->getName();
            if (!in_array($name, $optionNames, true)) {
                continue;
            }
            $defaultValue = $property->getValue($action);
            $tags         = $this->parseDocCommentTags($property);

            // Display camelCase options in kebab-case
            $name = Inflector::camel2id($name, '-', true);

            if (isset($tags['var']) || isset($tags['property'])) {
                $doc = isset($tags['var']) ? $tags['var'] : $tags['property'];
                if (is_array($doc)) {
                    $doc = reset($doc);
                }
                if (preg_match('/^(\S+)(.*)/s', $doc, $matches)) {
                    $type    = $matches[1];
                    $comment = $matches[2];
                } else {
                    $type    = null;
                    $comment = $doc;
                }
                $options[$name] = [
                    'type'    => $type,
                    'default' => $defaultValue,
                    'comment' => $comment,
                ];
            } else {
                $options[$name] = [
                    'type'    => null,
                    'default' => $defaultValue,
                    'comment' => '',
                ];
            }
        }

        return $options;
    }

}
