# Yii2 Artisan Bridge

This library brings the ease of Artisan commands and the power of Symfony console to Yii2 and Craft 5.

## Install

Require the package:
```
composer require ostark/yii2-artisan-bridge
```

### Configure actions for a Craft 5 plugin
```php
<?php namespace you\PluginName;

use Craft;
use craft\base\Plugin as BasePlugin;
use ostark\Yii2ArtisanBridge\ActionGroup;
use ostark\Yii2ArtisanBridge\Bridge;
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

            $group = (new ActionGroup('group-name', 'What this group is about.'))
                ->setActions([
                    'action1'  => ActionOne::class,
                    'action2'  => ActionTwo::class,
                ])
                ->setDefaultAction('action1')
                ->setOptions([
                    'one' => 'option-one',
                    'two' => 'option-two',
                    'option-without-alias'
                ]);
            
            Bridge::registerGroup($group);
        }
    }
}

```

### Write your Actions (Commands)

You write one class per action. Your actual instructions live in the `run()` method, similar to 
`execute()` in Symfony or `handle()` in Laravel. Command arguments map to the arguments of the `run()` method.

Options and option aliases are registered in Commands::register($prefix, $actions, `$options`). To access an option, 
it must be declared as a public property in the Action class. 

```php
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
```

### Artisan helper methods

**Prompting for input**

```php
$name = $this->ask('What is your name?', $default = null)`
```

```php
$name = $this->anticipate('What is your name?', ['Taylor', 'Fabien', 'Brad', 'Brandon']);
```

```php
if ($this->confirm('Do you wish to continue?')) {
    // continue
}
```


**Writing output**

```php
$this->info('Display this on the screen');
$this->error('Something went wrong!');
```

```php
$headers = ['Name', 'Email'];
$rows    = [['First name', 'First email'], ['Second name', 'Second email']];

$this->table($headers, $rows);
```



### Symfony block style

```php
$this->title('Title style block');
$this->section('Section style block');
$this->listing(['One','Two','Three'];

$this->successBlock('Yeah!');
$this->errorBlock('Oh no!');

// Custom blocks
$this->block($messages, $type = null, $style = null, $prefix = ' ', $padding = true, $escape = true);

```


### Symfony progress bar

```php
$items = range(1,10);

$bar = $this->output->createProgressBar(count($items));

// Custom format
$bar->setFormat('%message%' . PHP_EOL . '%bar% %percent:3s% %' . PHP_EOL . 'time:  %elapsed:6s%/%estimated:-6s%' . PHP_EOL.PHP_EOL);
$bar->setBarCharacter('<info>'.$bar->getBarCharacter().'</info>');
$bar->setBarWidth(80);

foreach ($items as $i) {
    sleep(1);
    $bar->advance();
    $bar->setMessage("My bar, some progress... $i");
}

$bar->finish();
```

## Custom formatter

Register a custom OutputFormatterStyle 
```php
// in your init()
\yii\base\Event::on(
    Commands::class,
    Commands::EVENT_BEFORE_ACTION,
    function (ActionEvent $event) {
        $style = new OutputFormatterStyle('white', 'cyan');
        $event->action->output->getFormatter()->setStyle('ocean', $style);
    }
);
```

Apply the style
```php
$this->title('Title in <ocean>blue</ocean>');
```
