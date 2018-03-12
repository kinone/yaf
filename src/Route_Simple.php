<?php
/**
 * Description of Route_Simple.php.
 *
 * @package Kinone\Yaf
 * @author zhenhao <phpcandy@163.com>
 */

namespace Kinone\Yaf;

final class Route_Simple implements Route_Interface
{
    /**
     * @var string
     */
    private $module;

    /**
     * @var string
     */
    private $controller;

    /**
     * @var string
     */
    private $action;

    public function __construct($moduleName, $controllername, $actionName)
    {
        $this->module = $moduleName;
        $this->controller = $controllername;
        $this->action = $actionName;
    }

    public function route(Request_Abstract $request)
    {
        $m = $request->getQuery($this->module);
        $c = $request->getQuery($this->controller);
        $a = $request->getQuery($this->action);

        if (!$m & !$c & !$a) {
            return false;
        }

        $request->setModuleName($m)
            ->setControllerName($c)
            ->setActionName($a)
            ->setRouted(true);

        return true;
    }

    /**
     * @param array $info
     * @param array $query
     * @return string
     */
    public function assemble(array $info, array $query = [])
    {
        $str = '?';
        if (isset($info[':m'])) {
            $str .= http_build_query([$this->module => strval($info[':m'])]) . '&';
        }

        if (isset($info[':c'])) {
            $str .= http_build_query([$this->controller => strval($info[':c'])]) . '&';
        } else {
            throw new Exception_TypeError('You should be specify the controller by :c');
        }

        if (isset($info[':a'])) {
            $str .= http_build_query([$this->action => strval($info[':a'])]);
        } else {
            throw new Exception_TypeError('You should be specify the action by :a');
        }

        if ($query) {
            $str .= '&' . http_build_query($query);
        }

        return $str;
    }
}
