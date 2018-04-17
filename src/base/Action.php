<?php namespace ostark\Yii2ArtisanBridge\base;

use ostark\Yii2ArtisanBridge\ConsoleOutput;
use ostark\Yii2ArtisanBridge\OutputStyle;
use Symfony\Component\Console\Input\ArgvInput;
use yii\base\Action as YiiBaseAction;

/**
 * Class Action
 *
 * @package ostark\Yii2ArtisanBridge\base
 *
 */
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

    /**
     * Action constructor.
     *
     * @param string               $id
     * @param \yii\base\Controller $controller
     * @param array                $config
     */
    public function __construct(string $id, \yii\base\Controller $controller, array $config = [])
    {
        parent::__construct($id, $controller, $config);

        $this->input = new ArgvInput();
        $this->output = new OutputStyle($this->input, new ConsoleOutput());

    }

}

