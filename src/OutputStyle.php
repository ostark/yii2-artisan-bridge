<?php

namespace ostark\Yii2ArtisanBridge;

use Symfony\Component\Console\Style\SymfonyStyle;

class OutputStyle extends SymfonyStyle
{

    /**
     * Emulate terminal typing
     *
     * @param string $command
     * @param string $style
     * @param int    $speed
     * @param string|null $prepend
     */
    public function type(string $command, string $style = "fg=white", $speed = 5, string $prepend = "$ ")
    {
        if (!is_null($prepend)) {
            $this->write($prepend);
        }

        $chars = str_split($command);

        foreach ($chars as $char) {

            $this->write(sprintf("<%s>%s</>", $style, $char));

            if (is_int($speed) && $speed > 0) {
                $delay = rand(10000, 1000000) / $speed;
                usleep($delay);
            }
        }

        // delay before new line
        usleep(1000000 / $speed);
        $this->newLine();
    }
}
