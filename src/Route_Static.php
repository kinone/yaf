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

        $arr = explode('/', trim($pathinfo, '/'));;
        switch ($count = count($arr)) {
            case $count == 1:
                $controller = array_shift($arr);
                break;
            case $count == 2:
                $controller = array_shift($arr);
                $action = array_shift($arr);
                break;
            case $count >= 3:
                $p = array_shift($arr);
                if (Application::isModuleName($p)) {
                    $module = $p;
                    $controller = array_shift($arr);
                } else {
                    $controller = $p;
                }
                $action = array_shift($arr);
                break;
        }

        if ($module) {
            $request->setModuleName($module);
        }
        if ($controller) {
            $request->setControllerName($controller);
        }
        if ($action) {
            $request->setActionName($action);
        }

        $params = [];
        foreach ($arr as $item) {
            if (!isset($last)) {
                $params[$item] = null;
                $last = $item;
            } else {
                $params[$last] = $item;
                unset($last);
            }
        }
        $request->setParam($params)
            ->setRouted(true);

        return true;
    }

    /**
     * @param string[] $info
     * @param array $query
     * @return string
     * @throws Exception
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
