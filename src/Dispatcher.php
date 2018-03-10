<?php
/**
 * Description of Dispatcher.php.
 *
 * @package Kinone\Yaf
 */

namespace Kinone\Yaf;

use Kinone\Http\ResponseAbstract;

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
     * @var
     */
    private $_autoRender = true;

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
    private $_defaultModule = 'index';

    /**
     * @var string
     */
    private $_defaultController = 'index';

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
     */
    public function dispatch(Request_Abstract $request)
    {
        $response = $request->isCli() ? new Response_Cli() : new Response_Http();
        return $response;
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
