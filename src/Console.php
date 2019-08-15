<?php
/**
 * Description of Console.php.
 *
 * @package Kinone\Yaf
 */

namespace Kinone\Yaf;

use Symfony\Component\Console\Application as Handler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\CommandLoader\CommandLoaderInterface;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class Console
 * @package Kinone\Yaf
 *
 * @method void setVersion($version)
 * @method void setName($name)
 * @method void setDispatcher(EventDispatcherInterface $dispatcher)
 * @method void setCommandLoader(CommandLoaderInterface $commandLoader)
 * @method void setHelperSet(HelperSet $helperSet)
 * @method void setDefinition(InputDefinition $definition)
 * @method void setAutoExit($boolean)
 * @method void setCatchExceptions($boolean)
 * @method bool areExceptionsCaught()
 * @method bool isAutoExitEnabled()
 * @method bool has(string $name)
 * @method string getName()
 * @method string getVersion()
 * @method string getLongVersion()
 * @method string getHelp()
 * @method string findNamespace($namespace)
 * @method string[] getNamespaces()
 * @method Command get($name)
 * @method Command find($name)
 * @method Command|null add(Command $command)
 * @method Command[] all($namespace = null)
 * @method InputDefinition getDefinition()
 * @method HelperSet function getHelperSet()
 */
final class Console
{
    /**
     * @var Console|null
     */
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
    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        return $this->handler->run($input, $output);
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
     * @param $commandName
     * @param bool $isSingleCommand
     * @return $this
     */
    public function setDefaultCommand($commandName, $isSingleCommand = false)
    {
        $this->handler->setDefaultCommand($commandName, $isSingleCommand);

        return $this;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $callable = [$this->handler, $name];

        if (is_callable($callable)) {
            return call_user_func_array($callable, $arguments);
        }

        throw new \RuntimeException("method not found");
    }
}
