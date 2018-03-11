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

    const METHOD_HEAD = 'HEAD';
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_PATCH = 'PATCH';
    const METHOD_DELETE = 'DELETE';
    const METHOD_PURGE = 'PURGE';
    const METHOD_OPTIONS = 'OPTIONS';
    const METHOD_TRACE = 'TRACE';
    const METHOD_CONNECT = 'CONNECT';

    public $module;

    public $controller;

    public $action;

    public $method;

    protected $params = [];

    protected $language;

    protected $_exception;

    protected $_baseUri;

    protected $_uri;

    protected $_pathinfo;

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
        return $this->getMethod() === self::METHOD_GET;
    }

    public function isPost()
    {
        return $this->getMethod() === self::METHOD_POST;
    }

    public function isHead()
    {
        return $this->getMethod() === self::METHOD_HEAD;
    }

    public function isDelete()
    {
        return $this->getMethod() == self::METHOD_DELETE;
    }

    public function isPatch()
    {
        return $this->getMethod() == self::METHOD_PATCH;
    }

    public function isPut()
    {
        return $this->getMethod() == self::METHOD_PUT;
    }

    public function isOptions()
    {
        return $this->getMethod() == self::METHOD_OPTIONS;
    }

    public function isCli()
    {
        return PHP_SAPI === 'cli';
    }

    public function getServer($name, $default = null)
    {
        return isset($_SERVER[$name]) ? $_SERVER[$name] : $default;
    }

    public function getEnv($name, $default = null)
    {
        return isset($_ENV[$name]) ? $_ENV[$name] : $default;
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

        return $this;
    }

    public function setControllerName($controller)
    {
        $this->controller = $controller;

        return $this;
    }

    public function setActionName($action)
    {
        $this->action = $action;

        return $this;
    }

    public function getMethod()
    {
        if (null == $this->method) {
            $this->method = $this->getServer('REQUEST_METHOD', self::METHOD_GET);
        }

        return $this->method;
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
        return $this->_uri;
    }

    public function getPathinfo()
    {
        return $this->_pathinfo;
    }

    public function setRequestUri($uri)
    {
        $this->_uri = $uri;
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
