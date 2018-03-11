<?php
/**
 * Description of Application.php.
 *
 * @package Kinone\Yaf
 */

namespace Kinone\Yaf;

class Application
{
    /**
     * @var self
     */
    private static $app;

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
    private $modules = ['Index'];

    /**
     * @var string
     */
    private $environ = 'product';

    /**
     * @var bool
     */
    private $running;

    /**
     * @var string
     */
    private $appDirectory;

    public function __construct($config, $environ = null)
    {
        $this->environ = $environ;

        if (is_array($config)) {
            $this->config = new Config_Simple($config);
        } else {
            $this->config = new Config_Ini($config, $environ);
        }

        self::$app = $this;

        $this->dispatcher = Dispatcher::getInstance();
        $this->appDirectory = $this->config->get('application.directory');

        $localLibary = $this->config->get('application.libaray');
        $globalLibary = ini_get('yaf.library');

        Loader::getInstance($localLibary, $globalLibary);
    }

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
     * @throws Exception_StartupError
     */
    public function run()
    {
        if ($this->running) {
            throw new Exception_StartupError("An application instance already run");
        }

        $this->running = true;

        if (PHP_SAPI === 'cli') {
            $request = new Request_Simple();
        } else {
            $request = new Request_Http();
        }

        $response = Dispatcher::getInstance()->dispatch($request);
        $response->response();
    }

    public function execute()
    {

    }

    /**
     * @return string
     */
    public function environ()
    {
        return $this->environ;
    }

    /**
     * @return Application
     */
    public static function app()
    {
        return self::$app;
    }

    public static function isModuleName($module)
    {
        if (null == self::$app) {
            return false;
        }

        return in_array(ucfirst(strtolower($module)), self::$app->modules);
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
        return $this->modules;
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
        
    }

    public function getAppDirectory()
    {
        return $this->config->get('application.directory');
    }
}
