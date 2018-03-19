<?php
/**
 * Description of Application.php.
 *
 * @package Kinone\Yaf
 * @author zhenhao <phpcandy@163.com>
 */

namespace Kinone\Yaf;

class Application
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
     * Application constructor.
     * @param $config
     * @param string|null $environ
     * @throws Exception
     */
    public function __construct($config, $environ = null)
    {
        self::$_app = $this;

        $this->dispatcher = Dispatcher::getInstance();
        $this->dispatcher->setRequest(new Request_Http());

        ob_start();

        if (!$environ) {
            $environ = ini_get('yaf.environ') ?: 'product';
        }
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
}
