<?php
/**
 * Description of Console.php.
 *
 * @package Kinone\Yaf
 */

namespace Kinone\Yaf;

use Symfony\Component\Console\Application as Handler;
use Symfony\Component\Console\Command\Command;

final class Console
{
    private static $ins = null;

    /**
     * @var Application
     */
    private $app;

    /**
     * @var Handler
     */
    private $handler;

    /**
     * Console constructor.
     * @param $config
     * @param $environ
     */
    public function __construct($config, $environ)
    {
        try {
            $this->app = new Application($config, $environ);
        } catch (\Exception $ex) {
            die($ex->getMessage());
        }
        $this->handler = new Handler();

        self::$ins = $this;
    }

    /**
     * @return Console
     */
    public static function ins()
    {
        return self::$ins;
    }

    /**
     * @param Bootstrap_Abstract|null $bootstrap
     * @return Console
     */
    public function bootstrap(Bootstrap_Abstract $bootstrap = null)
    {
        try {
            $this->app->bootstrap($bootstrap);
        } catch (\Exception $ex) {
            die($ex->getMessage());
        }

        return $this;
    }

    /**
     * @return Application
     */
    public function app()
    {
        return $this->app;
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function run()
    {
        return $this->handler->run();
    }

    /**
     * @param Command[] $commands
     * @return Console
     */
    public function addCommands(array $commands)
    {
        $this->handler->addCommands($commands);

        return $this;
    }

    /**
     * @param Command $command
     * @return Command|null
     */
    public function add(Command $command)
    {
        return $this->handler->add($command);
    }
}
