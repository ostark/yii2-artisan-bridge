<?php namespace ostark\Yii2ArtisanBridge\base;

use ostark\Yii2ArtisanBridge\ConsoleOutput;
use ostark\Yii2ArtisanBridge\OutputStyle;
use Symfony\Component\Console\Input\ArgvInput;
use yii\base\Action as YiiBaseAction;

abstract class Action extends YiiBaseAction
{

    /**
     * @var \ostark\Yii2ArtisanBridge\OutputStyle
     */
    protected $output;

    /**
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    protected $input;


    use ArtisanOutputTrait;
    use BlockOutputTrait;

    public function init()
    {
        parent::init();

        $this->input = new ArgvInput();
        $this->output = new OutputStyle($this->input, new ConsoleOutput());

    }
    /**
     * @param $question
     *
     * @return bool
     * @throws \yii\console\Exception
     */
    public function pleaseConfirm($question)
    {
        if ($this->getOption('force')) {
            return true;
        }
        if ($this->controller->confirm(PHP_EOL . $question, true)) {
            return true;
        }

        $this->block('Action was not executed.', 'error');

        return false;
    }

    public function remotePreCheck()
    {
        $plugin = Plugin::getInstance();
        try {
            $plugin->ssh->checkPlugin();
        } catch (CraftNotInstalledException $e) {
            $this->error($e->getMessage());
        } catch (PluginNotInstalledException $e) {
            $this->error($e->getMessage());
        } catch (RemoteException $e) {
            $this->error($e->getMessage());
        }
    }

}

