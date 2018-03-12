<?php
/**
 * Description of Route_Regex.php.
 *
 * @package Kinone\Yaf
 */

namespace Kinone\Yaf;

final class Route_Regex implements Route_Interface
{
    /**
     * @var string
     */
    private $_match;

    /**
     * @var array
     */
    private $_route;

    /**
     * @var array
     */
    private $_map;

    /**
     * @var array
     */
    private $_varify;

    /**
     * @var string
     */
    private $_reverse;

    /**
     * Route_Regex constructor.
     *
     * @param string $match
     * @param array $route
     * @param array $map
     * @param array $varify
     * @param string $reverse
     */
    public function __construct($match, array $route, array $map = [], array $varify = [], $reverse = '')
    {
        $this->_match = $match;
        $this->_route = $route;
        $this->_map = $map;
        $this->_varify = $varify;
        $this->_reverse = $reverse;
    }

    public function route(Request_Abstract $request)
    {
        $pathinfo = $request->getPathinfo();
        if (!$this->match($pathinfo, $args)) {
            return false;
        }

        if (isset($this->_route['module']) && strlen($this->_route['module'])) {
            if ($this->_route['module'][0] != ':') {
                $request->setModuleName($this->_route['module']);
            } else {
                $request->setModuleName($args[substr($this->_route['module'], 1)]);
            }
        }

        if (isset($this->_route['controller']) && strlen($this->_route['controller'])) {
            if ($this->_route['controller'][0] != ':') {
                $request->setControllerName($this->_route['controller']);
            } else {
                $request->setControllerName($args[substr($this->_route['controller'], 1)]);
            }
        }

        if (isset($this->_route['action']) && strlen($this->_route['action'])) {
            if ($this->_route['action'][0] != ':') {
                $request->setActionName($this->_route['action']);
            } else {
                $request->setActionName($args[substr($this->_route['action'], 1)]);
            }
        }

        foreach ($args as $k => $v) {
            $request->setParam($k, $v);
        }

        return true;
    }

    public function assemble(array $info, array $query = [])
    {
        // TODO: Implement assemble() method.
    }

    private function match($uri, &$args)
    {
        $ret = preg_match($this->_match, $uri, $args);

        foreach ($args as $k => $arg) {
            if (isset($this->_map[$k])) {
                $args[$this->_map[$k]] = $arg;
                unset($args[$k]);
            } else if (is_int($k)) {
                unset($args[$k]);
            }
        }

        return $ret;
    }
}
