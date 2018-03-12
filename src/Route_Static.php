<?php
/**
 * Description of Route_Static.php.
 *
 * @package Kinone\Yaf
 * @author zhenhao <phpcandy@163.com>
 */

namespace Kinone\Yaf;

final class Route_Static implements Route_Interface
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

        $request->setModuleName($module)
            ->setControllerName($controller)
            ->setActionName($action)
            ->setRouted(true);

        return true;
    }

    /**
     * @param string[] $info
     * @param array $query
     * @return string
     */
    public function assemble(array $info, array $query = [])
    {
        $tmp = [];
        if (isset($info[':m'])) {
            $tmp[] = strval($info[':m']);
        }

        if (!isset($info[':c']) || !($controller = strval($info[':c']))) {
            throw new Exception_TypeError('You should be specify the controller by :c');
        }
        $tmp[] = $controller;

        if (!isset($info[':a']) || !($action = strval($info[':a']))) {
            throw new Exception_TypeError('You should be specify the action by :a');
        }
        $tmp[] = $action;

        $uri = '/' . implode('/', $tmp);

        return $query ? $uri . '?' . http_build_query($query) : $uri;
    }

    public function match($uri)
    {
        // always true
        return true;
    }
}
