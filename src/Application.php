<?php
/**
 * Description of Application.php.
 *
 * @package Kinone\Yaf
 * @author zhenhao <phpcandy@163.com>
 */

namespace Kinone\Yaf;

use Pimple\Container;
use Pimple\Exception\ExpectedInvokableException;
use Pimple\Exception\FrozenServiceException;
use Pimple\Exception\InvalidServiceIdentifierException;
use Pimple\Exception\UnknownIdentifierException;
use Pimple\ServiceProviderInterface;

class Application implements \ArrayAccess
{
    /**
     * @var self
     */
    private static $_app;

    /**
     * @var Config_Abstract
     */
    private $config;

    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * @var array
     */
    private $_modules = ['Index'];

    /**
     * @var string
     */
    private $_environ = 'product';

    /**
     * @var bool
     */
    private $_running = false;

    /**
     * @var string
     */
    private $appDirectory;

    /**
     * @var Container
     */
    private $container;

    /**
     * Application constructor.
     * @param $config
     * @param string|null $environ
     * @throws Exception
     */
    public function __construct($config, $environ)
    {
        self::$_app = $this;

        $this->container = new Container();

        $this->dispatcher = Dispatcher::getInstance();
        $this->dispatcher->setRequest(new Request_Http());

        ob_start();

        $this->_environ = $environ;

        if (is_array($config)) {
            $this->config = new Config_Simple($config);
        } else {
            $this->config = new Config_Ini($config, $environ);
        }

        if (null !== ($throwException = $this->config->get('application.dispatcher.throwException'))) {
            $this->dispatcher->throwException(boolval($throwException));
        }

        if (null !== ($catchException = $this->config->get('application.dispatcher.catchException'))) {
            $this->dispatcher->catchException(boolval($catchException));
        }

        $this->appDirectory = $this->config->get('application.directory');

        if (!$this->appDirectory) {
            throw new Exception_StartupError('Expected a directory entry in application configures');
        }

        $modules = $this->config->get('application.modules');
        if ($modules) {
            $this->_modules = array_map('ucfirst', explode(',', $modules));
        }

        $localLibrary = $this->config->get('application.library');
        $globalLibrary = ini_get('yaf.library');
        Loader::getInstance($localLibrary, $globalLibrary);
    }

    public function __destruct()
    {
        ob_end_flush();
    }

    /**
     * @param Bootstrap_Abstract|null $bootstrap
     * @return $this
     * @throws Exception
     */
    public function bootstrap(Bootstrap_Abstract $bootstrap = null)
    {
        if ($bootstrap) {
            $ref = new \ReflectionObject($bootstrap);
        } else {
            try {
                $ref = new \ReflectionClass('Bootstrap');
            } catch (\Exception $exception) {
                throw new Exception($exception->getMessage());
            }
            $bootstrap = $ref->newInstance();
            if (!$bootstrap instanceof Bootstrap_Abstract) {
                throw new Exception_TypeError(sprintf('Bootstrap should be instance of %s', Bootstrap_Abstract::class));
            }
        }

        $methods = $ref->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach($methods as $m) {
            if (substr($m->getName(), 0, 5) == '_init') {
                $m->invoke($bootstrap, $this->dispatcher);
            }
        }

        return $this;
    }

    /**
     * @throws Exception
     */
    public function run()
    {
        if ($this->_running) {
            throw new Exception_StartupError("An application instance already run");
        }

        $this->_running = true;

        Dispatcher::getInstance()->dispatch();
    }

    /**
     * @param callable $callback
     * @param array $args
     * @return mixed
     */
    public function execute(callable $callback, ...$args)
    {
        return call_user_func_array($callback, $args);
    }

    /**
     * @return string
     */
    public function environ()
    {
        return $this->_environ;
    }

    /**
     * @return Application
     */
    public static function app()
    {
        return self::$_app;
    }

    public static function isModuleName($module)
    {
        if (null == self::$_app) {
            return false;
        }

        return in_array(ucfirst(strtolower($module)), self::$_app->_modules);
    }

    /**
     * @return Config_Abstract
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return array
     */
    public function getModules()
    {
        return $this->_modules;
    }

    /**
     * @return Dispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    public function setAppDirectory($path)
    {
        $this->appDirectory = $path;

        return $this;
    }

    public function getAppDirectory()
    {
        return $this->appDirectory;
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Marks a callable as being a factory service.
     *
     * @param callable $callable A service definition to be used as a factory
     *
     * @return callable The passed callable
     *
     * @throws ExpectedInvokableException Service definition has to be a closure or an invokable object
     */
    public function factory($callable)
    {
        return $this->container->factory($callable);
    }

    /**
     * Protects a callable from being interpreted as a service.
     *
     * This is useful when you want to store a callable as a parameter.
     *
     * @param callable $callable A callable to protect from being evaluated
     *
     * @return callable The passed callable
     *
     * @throws ExpectedInvokableException Service definition has to be a closure or an invokable object
     */
    public function protect($callable)
    {
        return $this->container->protect($callable);
    }

    /**
     * Gets a parameter or the closure defining an object.
     *
     * @param string $id The unique identifier for the parameter or object
     *
     * @return mixed The value of the parameter or the closure defining an object
     *
     * @throws UnknownIdentifierException If the identifier is not defined
     */
    public function raw($id)
    {
        return $this->container->raw($id);
    }

    /**
     * Extends an object definition.
     *
     * Useful when you want to extend an existing object definition,
     * without necessarily loading that object.
     *
     * @param string   $id       The unique identifier for the object
     * @param callable $callable A service definition to extend the original
     *
     * @return callable The wrapped callable
     *
     * @throws UnknownIdentifierException        If the identifier is not defined
     * @throws FrozenServiceException            If the service is frozen
     * @throws InvalidServiceIdentifierException If the identifier belongs to a parameter
     * @throws ExpectedInvokableException        If the extension callable is not a closure or an invokable object
     */
    public function extend($id, $callable)
    {
        return $this->container->extend($id, $callable);
    }

    /**
     * Returns all defined value names.
     *
     * @return array An array of value names
     */
    public function keys()
    {
        return $this->container->keys();
    }

    /**
     * Registers a service provider.
     *
     * @param ServiceProviderInterface $provider A ServiceProviderInterface instance
     * @param array                    $values   An array of values that customizes the provider
     *
     * @return Container
     */
    public function register(ServiceProviderInterface $provider, array $values = array())
    {
        return $this->container->register($provider, $values);
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return $this->container->offsetExists($offset);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->container->offsetGet($offset);
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->container->offsetSet($offset, $value);
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        $this->container->offsetUnset($offset);
    }
}
