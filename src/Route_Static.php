<?php
/**
 * Description of Route_Static.php.
 *
 * @package Kinone\Yaf
 */

namespace Kinone\Yaf;

use Symfony\Component\HttpFoundation\Request;

class Route_Static implements Route_Interface
{
    /**
     * @param Request_Abstract $request
     * @return bool
     */
    public function route(Request_Abstract $request)
    {
        $pathinfo = $request->getPathinfo();
        $module = null;
        $controller = null;
        $action = null;

        $arr = explode('/', trim($pathinfo, '/'));
        if (count($arr)) {
            $tmp = array_shift($arr);
            if (Application::isModuleName($tmp)) {
                $module = $tmp;
                $controller = array_shift($arr);
            } else {
                $controller = $tmp;
            }

            $action = array_shift($arr);
        }

        $params = [];
        foreach($arr as $item) {
            if (!isset($last)) {
                $params[$item] = null;
                $last = $item;
            } else {
                $params[$last] = $item;
                unset($last);
            }
        }
        foreach($params as $k => $v) {
            $request->setParam($k, $v);
        }

        $request->setModuleName($module);
        $request->setControllerName($controller);
        $request->setActionName($action);
        $request->setRouted(true);

        return true;
    }

    public function assemble(array $info, array $query = [])
    {
        // TODO: Implement assemble() method.
    }

    public function match($uri)
    {
        // always true
        return true;
    }
}
