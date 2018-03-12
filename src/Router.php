<?php
/**
 * Description of Router.php.
 *
 * @package Kinone\Yaf
 * @author zhenhao <phpcandy@163.com>
 */
namespace Kinone\Yaf;

class Router
{
    /**
     * @var Route_Interface[]
     */
    private $_routes = [];

    /**
     * @var Route_Interface
     */
    private $_current;

    public function __construct()
    {
        $this->addRoute('_default', new Route_Static());
    }

    /**
     * @param $name
     * @return Route_Interface
     */
    public function getRoute($name)
    {
        return isset($this->_routes[$name]) ? $this->_routes[$name] : null;
    }

    /**
     * @param $name
     * @param Route_Interface $route
     */
    public function addRoute($name, Route_Interface $route)
    {
        $this->_routes[$name] = $route;
    }

    public function getCurrentRoute()
    {
        return $this->_current;
    }

    public function route(Request_Abstract $request)
    {
        $route = end($this->_routes);
        while ($route) {
            if($route->route($request)) {
                $this->_current = $route;
                return true;
            }
            $route = prev($this->_routes);
        }

        return false;
    }

    public function addConfig(Config_Abstract $config)
    {
        foreach ($config as $name => $item) {
            if (!$item instanceof Config_Abstract) {
                continue;
            }
            $type = strval($item->get('type'));

            switch ($type) {
                case 'simple':
                    $this->addRoute($name, new Route_Simple(
                        $item->get('module'),
                        $item->get('controller'),
                        $item->get('action')
                    ));
                    break;
                case 'supervar':
                    $this->addRoute($name, new Route_Supervar(
                        $item->get('varname')
                    ));
                    break;
                case 'map':
                    $this->addRoute($name, new Route_Map(
                        $item->get('controllerPrefer'),
                        $item->get('delimiter')
                    ));
                    break;
                case 'rewrite':
                    $route = $item->get('route');
                    $info = $item->get('default');
                    $info = $info instanceof Config_Abstract ? $info->toArray() : [];
                    $this->addRoute($name, new Route_Rewrite($route, $info));
                    break;
                case 'regex':
                    $route = $item->get('route');
                    $info = $item->get('default');
                    $map = $item->get('map');
                    $info = $info instanceof Config_Abstract ? $info->toArray() : [];
                    $map = $map instanceof Config_Abstract ? $map->toArray() : [];
                    $this->addRoute($name, new Route_Regex($route, $info, $map));
                    break;
            }
        }
    }
}
