<?php
/**
 * Description of Router.php.
 *
 * @package \
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
        $routes = array_reverse($this->_routes);

        foreach($routes as $route) {
            if($route->route($request)) {
                $this->_current = $route;
                return true;
            }
        }

        return false;
    }

    public function addConfig(Config_Abstract $config)
    {

    }
}
