<?php
/**
 * Description of Controller_Abstract.php.
 *
 * @package Kinone\Yaf
 */

namespace Kinone\Yaf;

abstract class Controller_Abstract
{
    private $_actions;

    /**
     * @var string
     */
    private $_module;

    /**
     * @var string
     */
    private $_name;

    /**
     * @var Request_Abstract
     */
    private $_request;

    /**
     * @var Response_Abstract
     */
    private $_response;

    /**
     * @var View_Interface
     */
    private $_view;

    final public function __construct(Request_Abstract $request, Response_Abstract $response, View_Interface $view)
    {
        $this->_request = $request;
        $this->_response = $response;
        $this->_view = $view;
        $this->_name = strtolower(substr(static::class, 0, -10));

        $this->init();
    }

    public function init()
    {

    }

    public function forward($module, $controller = null, $action = null, $params = null)
    {
        
    }

    public function redirect($url)
    {
        
    }

    public function getModuleName()
    {
        return $this->_module;
    }

    /**
     * @return Request_Abstract
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * @return View_Interface
     */
    public function getView()
    {
        return $this->_view;
    }

    public function initView($vars = [])
    {

    }

    /**
     * @return Response_Abstract
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * @param string $path
     * @return Controller_Abstract
     */
    public function setViewPath($path)
    {
        $this->_view->setScriptPath($path);
        
        return $this;
    }

    /**
     * @return string
     */
    public function getViewPath()
    {
        return $this->_view->getScriptPath();
    }

    /**
     * @param string $tpl
     * @param array $vars
     */
    public function dispaly($tpl, $vars = [])
    {
        $tpl = implode(DIRECTORY_SEPARATOR, [$this->_name, $tpl . '.php']);
        $this->_view->dispaly($tpl, $vars);
    }

    /**
     * @param string $tpl
     * @param array $vars
     * @return string
     */
    public function render($tpl, $vars = [])
    {
        $tpl = implode(DIRECTORY_SEPARATOR, [$this->_name, $tpl . '.php']);
        return $this->_view->render($tpl, $vars);
    }
}
