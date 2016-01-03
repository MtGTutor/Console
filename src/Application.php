<?php
namespace MtGTutor\Console;

use Symfony\Component\Console\Application as BaseApplication;

/**
 * Class Application
 * @package MtGTutor\Console
 */
class Application extends BaseApplication
{
    /**
     * @var string
     */
    const NAME = 'MtG-Tutor.de Helpers CLI';

    /**
     * @var string
     */
    const VERSION = '1.0';

    /**
     * Application constructor.
     */
    public function __construct()
    {
        $name =
            '        __  ___ __   ______      ______        __                     __    ' . PHP_EOL .
            '       /  |/  // /_ / ____/     /_  __/__  __ / /_ ____   _____  ____/ /___ ' . PHP_EOL .
            '      / /|_/ // __// / __ ______ / /  / / / // __// __ \ / ___/ / __  // _ \\' . PHP_EOL .
            '     / /  / // /_ / /_/ //_____// /  / /_/ // /_ / /_/ // /  _ / /_/ //  __/' . PHP_EOL .
            '    /_/  /_/ \__/ \____/       /_/   \__,_/ \__/ \____//_/  (_)\__,_/ \___/ ' . PHP_EOL .
            '                                                                            ' . PHP_EOL .
            '           __  __       __                            ______ __     ____' . PHP_EOL .
            '          / / / /___   / /____   ___   _____ _____   / ____// /    /  _/' . PHP_EOL .
            '         / /_/ // _ \ / // __ \ / _ \ / ___// ___/  / /    / /     / /  ' . PHP_EOL .
            '        / __  //  __// // /_/ //  __// /   (__  )  / /___ / /___ _/ /   ' . PHP_EOL .
            '       /_/ /_/ \___//_// .___/ \___//_/   /____/   \____//_____//___/   ' . PHP_EOL .
            '                      /_/                                               ' . PHP_EOL;

        parent::__construct($name . static::NAME, static::VERSION);
    }
}
