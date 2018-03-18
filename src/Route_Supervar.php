<?php
/**
 * Description of Route_Supervar.php.
 *
 * @package Kinone\Yaf
 * @author zhenhao <phpcandy@163.com>
 */

namespace Kinone\Yaf;

final class Route_Supervar implements Route_Interface
{
    /**
     * @var string
     */
    private $varname;

    public function __construct($varname)
    {
        $this->varname = $varname;
    }

    public function route(Request_Abstract $request)
    {
        $var = $request->getQuery($this->varname);
        if (!$var) {
            return false;
        }

        $arr = array_filter(explode('/', trim($var, '/')));
        switch($count = count($arr)) {
            case $count == 1:
                $action = array_shift($arr);
                $request->setActionName($action);
                break;
            case $count == 2:
                $controller = array_shift($arr);
                $action = array_shift($arr);
                $request->setControllerName($controller)
                    ->setActionName($action);
                break;
            case $count >= 3:
                $module = array_shift($arr);
                $controller = array_shift($arr);
                $action = array_shift($arr);
                $request->setModuleName($module)
                    ->setControllerName($controller)
                    ->setActionName($action);
                break;
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
     * @param array $info
     * @param array $query
     * @return string
     * @throws Exception
     */
    public function assemble(array $info, array $query = [])
    {
        $uri = '?';
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

        $uri .= http_build_query([$this->varname => '/' . implode('/', $tmp)]);

        if ($query) {
            $uri .= '&' . http_build_query($query);
        }

        return $uri;
    }
}
