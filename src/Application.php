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
    private $modules = [];

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
        }

        $methods = $ref->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach($methods as $m) {
            if (substr($m->getName(), 0, 5) == '_init') {
                $m->invoke($bootstrap, $this->dispatcher);
            }
        }

        return $this;
    }

    public function run()
    {
        $this->execute();
    }

    public function execute()
    {
        if (PHP_SAPI === 'cli') {
            $request = new Request_Simple();
        } else {
            $request = new Request_Http();
        }

        $response = Dispatcher::getInstance()->dispatch($request);
        $response->response();
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
