<?php
/**
 * Description of Route_Map.php.
 *
 * @package Kinone\Yaf
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
            foreach($params as $k => $v) {
                $request->setParam($k, $v);
            }
        }

        return true;
    }

    public function assemble(array $info, array $query = [])
    {
        // TODO: Implement assemble() method.
    }
}
