<?php

namespace ostark\Yii2ArtisanBridge;

use Symfony\Component\Console\Input\ArgvInput;
use yii\console\UnknownCommandException;


/**
 * Symfony console I/O
 *
 * @package ostark\Yii2ArtisanBridge
 */
class ErrorHandler extends \yii\console\ErrorHandler
{

    /**
     * @param \Exception $exception
     */
    protected function renderException($exception)
    {
        if ($exception instanceof UnknownCommandException) {
            parent::renderException($exception);
        }

        if (!YII_DEBUG) {
            return $this->render($exception->getMessage(), [get_class($exception)]);
        }

        // Debug mode
        $messages = [
            get_class($exception),
            "in " . dirname($exception->getFile()) . DIRECTORY_SEPARATOR .
            basename($exception->getFile()) . ": " . $exception->getLine()
        ];

        if ($exception instanceof \yii\db\Exception && !empty($exception->errorInfo)) {
            $messages[] = print_r($exception->errorInfo, true);
        }

        return $this->render($exception->getMessage(), $messages, $exception->getTraceAsString());

    }

    /**
     * @param string      $title
     * @param array       $messages
     * @param string|null $trace
     *
     * @return string|void
     */
    protected function render(string $title, array $messages, string $trace = null)
    {
        $output   = new OutputStyle(new ArgvInput(), new ConsoleOutput());
        $messages = array_merge(["<fg=white;bg=red;options=bold>$title</>"], $messages);

        $output->block($messages, 'ERROR', 'fg=white;bg=red', ' ', true, false);

        if ($trace) {
            $trace = str_replace(\Yii::getAlias('@root'), '', $trace);
            $trace = "<fg=red;options=bold>Stack trace:</>" . PHP_EOL . PHP_EOL . $trace;
            $output->block($trace, null, 'fg=white', '<fg=red>▓ </> ', true, false);
        }
    }
}
