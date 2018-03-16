<?php
/**
 * Description of Controller_Abstract.php.
 *
 * @package Kinone\Yaf
 * @author zhenhao <phpcandy@163.com>
 */

namespace Kinone\Yaf;

abstract class Controller_Abstract
{
    public $_actions;

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

    private $_invokeArgs = [];

    final public function __construct(Request_Abstract $request, Response_Abstract $response, View_Interface $view, $invokeArgs = [])
    {
        $this->_request = $request;
        $this->_response = $response;
        $this->_view = $view;
        $this->_name = strtolower(substr(static::class, 0, -10));
        $this->_module = $request->getModuleName();
        $this->_invokeArgs = $invokeArgs;

        $this->init();
    }

    public function init()
    {

    }

    public function forward($module, $controller = null, $action = null, $params = null)
    {
        if (!$this->_request instanceof Request_Abstract) {
            return false;
        }

        $args = func_get_args();
        $argsCount = count($args);
        switch ($argsCount) {
            case 1:
                $this->_request->setActionName($args[0]);
                break;
            case 2:
                if (is_array($controller)) {
                    $this->_request->setActionName($args[0]);
                    $this->_request->setParam($args[1]);
                } else {
                    $this->_request->setControllerName($args[0])
                        ->setActionName($args[1]);
                }
                break;
            case 3:
                if (is_array($action)) {
                    $this->_request->setControllerName($args[0])
                        ->setActionName($args[1])
                        ->setParam($args[2]);
                } else {
                    $this->_request->setModuleName($args[0])
                        ->setControllerName($args[1])
                        ->setActionName($args[2]);
                }
                break;
            case 4:
                $this->_request->setModuleName($module)
                    ->setControllerName($controller)
                    ->setActionName($action)
                    ->setParam($params);
                break;
        }

        $this->_request->setDispatched(false);
        return true;
    }

    public function redirect($url)
    {
        $this->getResponse()->setRedirect($url);
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

    /**
     * @param array $options
     * @return $this
     */
    public function initView($options = [])
    {
        return $this;
    }

    /**
     * @return array
     */
    public function getInvokeArgs()
    {
        return $this->_invokeArgs;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getInvodeArg($name)
    {
        return (isset($this->_invokeArgs[$name])) ? $this->_invokeArgs[$name] : null;
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
        $name = str_replace('_', DIRECTORY_SEPARATOR, $this->_name);
        if ($tpl[0] !== '/') {
            $ext = Application::app()->getConfig()->get('application.view.ext') ?: 'php';
            $tpl = implode(DIRECTORY_SEPARATOR, [$name, $tpl . '.' . $ext]);
        }
        $this->_view->dispaly($tpl, $vars);
    }

    /**
     * @param string $tpl
     * @param array $vars
     * @return string
     */
    public function render($tpl, $vars = [])
    {
        $name = str_replace('_', DIRECTORY_SEPARATOR, $this->_name);
        if ($tpl[0] !== '/') {
            $ext = Application::app()->getConfig()->get('application.view.ext') ?: 'php';
            $tpl = implode(DIRECTORY_SEPARATOR, [$name, $tpl . '.' . $ext]);
        }
        return $this->_view->render($tpl, $vars);
    }
}
