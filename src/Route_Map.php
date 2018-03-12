<?php
/**
 * Description of Route_Map.php.
 *
 * @package Kinone\Yaf
 * @author zhenhao <phpcandy@163.com>
 */

namespace Kinone\Yaf;

final class Route_Map implements Route_Interface
{
    /**
     * @var bool
     */
    private $ctlPrefer;

    /**
     * @var string
     */
    private $delimeter;

    public function __construct($ctlPerfer = false, $delimeter = '')
    {
        $this->ctlPrefer = $ctlPerfer;
        $this->delimeter = trim($delimeter);
    }

    public function route(Request_Abstract $request)
    {
        $pathinfo = $request->getPathinfo();
        $l = strlen($this->delimeter);
        $rest = '';
        if ($l > 0
            && ($pos = strpos($pathinfo, $this->delimeter)) > 0
            && $pathinfo[$pos - $l] == '/'
        ) {
            if (isset($pathinfo[$pos + $l]) && $pathinfo[$pos + $l] == '/') {
                $rest = substr($pathinfo, $pos + $l);
                $pathinfo = substr($pathinfo, 0, $pos);
            }
        }

        $pathinfo = trim($pathinfo, '/');
        $rest = trim($rest, '/');

        if ($pathinfo) {
            if ($this->ctlPrefer) {
                $request->setControllerName(str_replace('/', '_', $pathinfo));
            } else {
                $request->setActionName(str_replace('/', '_', $pathinfo));
            }
        }

        if ($rest) {
            $arr = explode('/', $rest);
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
            $request->setParam($params);
        }

        return true;
    }

    public function assemble(array $info, array $query = [])
    {
        if ($this->ctlPrefer) {
            if (!isset($info[':c'])) {
                throw new Exception_TypeError('undefined the \'controller\' parameter for 1st parameter');
            } else {
                $pname = strval($info[':c']);
            }
        } else {
            if (!isset($info[':a'])) {
                throw new Exception_TypeError('undefined the \'action\' parameter for 1st parameter');
            } else {
                $pname = strval($info[':a']);
            }
        }

        $uri = '/' . str_replace('_', '/', $pname);

        if ($query) {
            if ($this->delimeter) {
                $uri .= '/' . $this->delimeter;
                foreach($query as $k => $v) {
                    $uri = implode('/', [$uri, $k, $v]);
                }
            } else {
                $uri .= '?' . http_build_query($query);
            }
        }

        return $uri;
    }
}
