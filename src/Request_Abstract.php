<?php
/**
 * Description of Request_Abstract.php.
 *
 * @package Kinone\Yaf
 */

namespace Kinone\Yaf;

abstract class Request_Abstract
{
    const SCHEME_HTTP = 'http';
    const SCHEME_HTTPS = 'https';

    public $module;

    public $controller;

    public $action;

    public $method;

    protected $params = [];

    protected $language;

    protected $_exception;

    protected $_baseUri;

    protected $uri;

    protected $dispatched = false;

    protected $routed = false;

    abstract public function get($name, $default = null);
    abstract public function getQuery($name, $default = null);
    abstract public function getPost($name, $default = null);
    abstract public function getRequest($name, $default = null);
    abstract public function getCookie($name, $default = null);
    abstract public function getFiles($name, $default = null);
    abstract public function isXmlHttpRequest($name, $default = null);

    public function isGet()
    {
        return $this->method === 'GET';
    }

    public function isPost()
    {
        return $this->method === 'POST';
    }

    public function isHead()
    {
        return $this->method === 'HEAD';
    }

    public function isDelete()
    {
        return $this->method == 'DELETE';
    }

    public function isPatch()
    {
        return $this->method == 'PATCH';
    }

    public function isPut()
    {
        return $this->method == 'PUT';
    }

    public function isOptions()
    {
        return $this->method == 'OPTIONS';
    }

    public function isCli()
    {
        return PHP_SAPI === 'cli';
    }

    public function getServer($name)
    {
        
    }

    public function getEnv($name, $default = 0)
    {

    }

    public function setParam($name, $value)
    {
        $this->params[$name] = $value;
    }

    public function getParam($name, $default = null)
    {
        return isset($this->params[$name]) ? $this->params[$name] : $default;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function getException()
    {
        return $this->_exception;
    }

    public function getModuleName()
    {
        return $this->module;
    }

    public function getControllerName()
    {
        return $this->controller;
    }

    public function getActionName()
    {
        return $this->action;
    }

    public function setModuleName($module)
    {
        $this->module = $module;
    }

    public function setControllerName($controller)
    {
        $this->controller = $controller;
    }

    public function setActionName($action)
    {
        $this->action = $action;
    }

    public function getMethod()
    {
        
    }

    public function getLanguage()
    {
        
    }

    public function setBaseUri($baseUri)
    {
        $this->_baseUri = $baseUri;

        return $this;
    }

    public function getBaseUri()
    {
        return $this->_baseUri;
    }

    public function getRequestUri()
    {
        return $this->uri;
    }

    public function setRequestUri($uri)
    {
        $this->uri = $uri;
    }

    public function isDispatched()
    {
        return $this->dispatched;
    }

    public function setDispatched($flag)
    {
        $this->dispatched = $flag;

        return $this;
    }

    public function isRouted()
    {
        return $this->routed;
    }

    public function setRouted($flag)
    {
        $this->routed = $flag;

        return $this;
    }
}
