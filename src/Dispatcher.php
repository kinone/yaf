<?php
/**
 * Description of Dispatcher.php.
 *
 * @package Kinone\Yaf
 */

namespace Kinone\Yaf;

final class Dispatcher
{
    /**
     * @var Router
     */
    private $_router;

    /**
     * @var View_Interface
     */
    private $_view;

    /**
     * @var Request_Abstract
     */
    private $_request;

    /**
     * @var Plugin_Abstract
     */
    private $_plugins;

    /**
     * @var bool
     */
    private $_autoRender = true;

    /**
     * @var bool
     */
    private $_instantlyFlush = false;

    /**
     * @var bool
     */
    private $_returnResponse = false;

    /**
     * @var bool
     */
    private $_throwException = true;

    /**
     * @var bool
     */
    private $_catchException = true;

    /**
     * @var string
     */
    private $_defaultModule = 'Index';

    /**
     * @var string
     */
    private $_defaultController = 'Index';

    /**
     * @var string
     */
    private $_defaultAction = 'index';

    /**
     * @var self
     */
    private static $_instance;

    private function __construct()
    {
        $this->_router = new Router();
        $this->_plugins = [];
    }

    /**
     * @return Dispatcher
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * @param Request_Abstract $request
     *
     * @return Response_Abstract
     * @throws Exception
     */
    public function dispatch(Request_Abstract $request)
    {
        $this->_request = $request;
        $response = $request->isCli() ? new Response_Cli() : new Response_Http();

        if (!$request->isRouted()) {

            $this->_notifyPlugins('routerStartup', $response);
            if (!$this->_router->route($request)) {
                throw new Exception_RouterFailed('route failed');
            }

            $this->_notifyPlugins('routerShutdown', $response);
        }
        $this->_fixDefault($request);
        $this->_notifyPlugins('dispatchLoopStartup', $response);

        $this->initView(null);

        $nesting = 5;

        do {
            $this->_notifyPlugins('preDispatch', $response);

            $this->_handle($response);

            $this->_fixDefault($request);

            $this->_notifyPlugins('postDispatch', $response);
        } while (--$nesting > 0 && !$this->_request->isDispatched());

        $this->_notifyPlugins('dispatchLoopShutdown', $response);

        if ($nesting == 0 && !$request->isDispatched()) {
            throw new Exception_DispatchFailed(sprintf('The max dispatch nesting %d was reached', 5));
        }

        if (!$this->_returnResponse) {
            $response->response();
            $response->clearBody();
        }

        return $response;
    }

    private function _handle(Response_Abstract $response)
    {
        $appDir = Application::app()->getAppDirectory();
        if (!$appDir) {
            throw new Exception_StartupError(
                sprintf('%s requires %s(which set the application.directory) to be initialized first', Dispatcher::class, Application::class)
            );
        }

        $this->_request->setDispatched(true);

        $module = $this->_request->getModuleName();
        $controller = $this->_request->getControllerName();
        $action = $this->_request->getActionName();

        if (!$module) {
            throw new Exception_DispatchFailed('Unexcepted a empty module name');
        } else if (!Application::isModuleName($module)) {
            throw new Exception_LoadFailed_Module(sprintf('There is no module %s', $module));
        }

        if (!$controller) {
            throw new Exception_DispatchFailed('Unexcepted a empty controller name');
        }

        $controllerObject = $this->_genController($appDir, $module, $controller, $response);
        if (!$this->_request->isDispatched()) {
            // forward is called in init function
            $this->_handle($response);
        }

        if ($module == $this->_defaultModule) {
            $tplDir = implode(DIRECTORY_SEPARATOR, [$appDir, 'views']);
        } else {
            $tplDir = implode(DIRECTORY_SEPARATOR, [$appDir, 'modules', $module, 'views']);
        }

        $this->_view->setScriptPath($tplDir);

        $func = strtolower($action) . 'Action';

        if (method_exists($controllerObject, $func)) {
            call_user_func_array([$controllerObject, $func], $this->_request->getParams());
        } else {
            throw new Exception_LoadFailed_Action(sprintf('There is no %s in %s', $func, get_class($controllerObject)));
        }

        if ($this->_autoRender) {
            if (!$this->_instantlyFlush) {
                $content = $controllerObject->render($action);
                $response->appendBody($content);
            } else {
                $controllerObject->dispaly($action);
            }
        }
    }

    /**
     * @param $appDir
     * @param $module
     * @param $controller
     * @param Response_Abstract $response
     * @return Controller_Abstract
     */
    private function _genController($appDir, $module, $controller, Response_Abstract $response)
    {
        if ($module != $this->_defaultModule) {
            $file = implode(DIRECTORY_SEPARATOR, [$appDir, 'modules', $module, 'controllers', $controller]) . '.php';
            Loader::import($file);
        }
        $controllerName = ucfirst($controller) . 'Controller';

        return new $controllerName($this->_request, $response, $this->_view);
    }

    private function _fixDefault(Request_Abstract $request)
    {
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();

        if (!$module) {
            $request->setModuleName($this->_defaultModule);
        } else {
            $request->setModuleName(ucfirst(strtolower($module)));
        }

        if (!$controller) {
            $request->setControllerName($this->_defaultController);
        } else {
            $request->setControllerName(ucfirst(strtolower($controller)));
        }

        if (!$action) {
            $request->setActionName($this->_defaultAction);
        } else {
            $request->setActionName(strtolower($action));
        }
    }

    private function _notifyPlugins($event, Response_Abstract $response)
    {
        foreach ($this->_plugins as $plugin) {
            if (!method_exists($plugin, $event)) {
                continue;
            }

            call_user_func_array([$plugin, $event], [$this->_request, $response]);
        }
    }

    public function registerPlugin(Plugin_Abstract $plugin)
    {
        $this->_plugins[] = $plugin;

        return $this;
    }

    public function enableView()
    {
        $this->_autoRender = true;
    }

    public function disableView()
    {
        $this->_autoRender = false;
    }

    public function initView($tplDir, $options = [])
    {
        $this->_view = new View_Simple($tplDir);
    }

    public function setView(View_Interface $view)
    {
        $this->_view = $view;

        return $this;
    }

    public function getView(View_Interface $view)
    {
        return $this->_view;
    }

    public function setRequest(Request_Abstract $request)
    {
        $this->_request = $request;

        return $this;
    }

    public function getApplication()
    {
        return Application::app();
    }

    public function getRouter()
    {
        return $this->_router;
    }

    public function getRequest()
    {
        return $this->_request;
    }

    public function setErrorHandler(callable $callable)
    {

    }

    public function setDefaultModule($module)
    {
        $this->_defaultModule = $module;

        return $this;
    }

    public function setDefaultController($controller)
    {
        $this->_defaultController = $controller;

        return $this;
    }

    public function setDefaultAction($action)
    {
        $this->_defaultAction = $action;

        return $this;
    }

    public function returnResponse($flag = null)
    {
        if (null === $flag) {
            return $this->_returnResponse;
        }

        $this->_returnResponse = $flag;

        return $this;
    }

    public function autoRender($flag = null)
    {
        if (null === $flag) {
            return $this->_autoRender;
        }

        $this->_autoRender = $flag;

        return $this;
    }

    public function throwException($flag = null)
    {
        if ($flag === null) {
            return $this->_throwException;
        }

        $this->_throwException = $flag;

        return $this;
    }

    public function catchException($flag = null)
    {
        if (null === $flag) {
            return $this->_catchException;
        }

        $this->_catchException = $flag;

        return $this;
    }
}
