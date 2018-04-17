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

### Write your Actions (Commands)

You write one class per action. You actual instructions live in the `run()` method, similar to 
`execute()` in Symfony or `handle()` in Laravel. Command arguments map to the arguments of the `run()` method.

Options and option aliases are registered in Commands::register($prefix, $actions, `$options`). To access an option, 
it must be declared as a public property in the Action class. 

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
```

### Artisan helper methods

**Prompting for input**

```
$name = $this->ask('What is your name?', $default = null)`
```
```
$name = $this->anticipate('What is your name?', ['Taylor', 'Fabien', 'Brad', 'Brandon']);
```
```
if ($this->confirm('Do you wish to continue?')) {
    // continue
}
```
**Writing output**

```
$this->info('Display this on the screen');
$this->error('Something went wrong!');
```

```
$headers = ['Name', 'Email'];
$rows    = [['First name', 'First email'], ['Second name', 'Second email']];

$this->table($headers, $rows);
```

### Symfony block style

```
$this->title('Title style block');
$this->section('Section style block');
$this->listing(['One','Two','Three'];

$this->successBlock('Yeah!');
$this->errorBlock('Oh no!');

``

### Symfony progress bar

```
$items = range(1,10);

$bar = $this->output->createProgressBar(count($items));

// Custom format
$bar->setFormat('%message%' . PHP_EOL . '%bar% %percent:3s% %' . PHP_EOL . 'time:  %elapsed:6s%/%estimated:-6s%' . PHP_EOL.PHP_EOL);
$bar->setBarCharacter('<info>'.$bar->getBarCharacter().'</info>');
$bar->setBarWidth(80);

foreach ($items as $i) {`
    sleep(1);
    $bar->advance();
    $bar->setMessage("My bar, some progress... $i");
}

$bar->finish();
```
