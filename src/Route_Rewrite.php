<?php
/**
 * Description of Route_Rewrite.php.
 *
 * @package Kinone\Yaf
 * @author zhenhao <phpcandy@163.com>
 */

namespace Kinone\Yaf;

final class Route_Rewrite implements Route_Interface
{
    private $_match;
    private $_route;
    private $_varify;

    public function __construct($match, array $route, array $verify = [])
    {
        $this->_match = $match;
        $this->_route = $route;
        $this->_varify = $verify;
    }

    public function route(Request_Abstract $request)
    {
        $pathinfo = trim($request->getPathinfo(), '/');
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

    /**
     * @param array $info
     * @param array $query
     * @return string
     */
    public function assemble(array $info, array $query = [])
    {
        $segs = explode('/', $this->_match);
        foreach($segs as $i => $seg) {
            if ($seg === '*') {
                $segs[$i] = '';
                foreach($info as $k => $v) {
                    if (!is_int($k)) {
                        $segs[$i] = implode('/', [$segs[$i], $k, strval($v)]);
                    }
                }
                $segs[$i] = ltrim($segs[$i], '/');
                break;
            } else if (strlen($seg) && $seg[0] == ':') {
                if (isset($info[$seg])) {
                    $segs[$i] = strval($info[$seg]);
                    unset($info[$seg]);
                }
            }
        }

        $uri = implode('/', $segs);

        if ($query) {
            $uri .= '?' . http_build_query($query);
        }

        return $uri;
    }

    protected function match($uri, &$matches)
    {
        if (!$uri) {
            return false;
        }

        $pattern = '#^';
        $segs = explode('/', trim($this->_match, '/'));
        foreach($segs as $seg) {
            if ($seg === '*') {
                $pattern .= '(?<__yaf_route_rest>.*)';
                break;
            } else if ($seg[0] === ':') {
                $pattern .= '(?<' . substr($seg, 1) . '>[^/]+)';
            } else {
                $pattern .= $seg . '/';
            }
        }
        $pattern .= '#i';

        $ret = preg_match($pattern, $uri, $matches);
        foreach($matches as $k => $v) {
            if (is_int($k)) {
                unset($matches[$k]);
            }
        }

        if (isset($matches['__yaf_route_rest'])) {
            $rest = trim($matches['__yaf_route_rest'], '/');
            if ($rest) {
                $arr = explode('/', $rest);
                foreach ($arr as $item) {
                    if (!isset($last)) {
                        $matches[$item] = null;
                        $last = $item;
                    } else {
                        $matches[$last] = $item;
                        unset($last);
                    }
                }
            }

            unset($matches['__yaf_route_rest']);
        }

        return $ret;
    }
}
