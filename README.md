# Yii2 Artisan Bridge

This library brings the ease of Artisan commands and the power of Symfony console to Yii2 and Craft3.

## Install

Require the package:
```
composer require ostark/yii2-artisan-bridge
```

### Configure actions for a Craft3 plugin
```
<?php namespace you\PluginName;

use Craft;
use craft\base\Plugin as BasePlugin;
use you\PluginName\actions\ActionOne;
use you\PluginName\actions\ActionTwo;

/**
 * Class Plugin
 */
class Plugin extends BasePlugin
{
    /**
     * Initialize Plugins
     */
    public function init()
    {
        parent::init();

        if (Craft::$app instanceof \yii\console\Application) {

            // Register console commands
            Commands::register('prefix', [
                'action1'  => ActionOne::class,
                'action2'  => ActionTwo::class,
            ], [
                 'one' => 'option-one',
                 'two' => 'option-two',
                 'option-without-alias'
            ]);
        
    }
}

```

### Write your Actions

```
<?php namespace you\PluginName\actions;

use Craft;
use ostark\Yii2ArtisanBridge\base\Action as BaseAction;
use yii\console\ExitCode;

class ActionOne extends BaseAction {

    public $optionOne = 'default-value';
    
    /**
      * Ask some question
      *
      * @param string $name
      * @return bool
      */
    public function run($name) {
    
        $this->title("Hello {$name}, 'option-one' is '{$this->optionOne}'");
        
        $answer = $this->choice("What's your favorite animal?", ['Dog','Cat','Elephant']);
        
        if ($answer === 'Elephant') {
            $this->successBlock("'$answer' is correct.");
            return ExitCode::OK;
        } else {
            $this->errorBlock("'$answer' is the wrong.");
            return ExitCode::UNSPECIFIED_ERROR;
        }
    
    }

}   
